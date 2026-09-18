<?php

namespace App\Services\Security;

use Exception;
use Illuminate\Support\Facades\File;

class QuestionVaultService
{
    protected string $vaultPath;

    protected string $masterKey;

    protected string $cipher;

    protected string $activeKeyId;

    protected bool $enabled;

    protected ?array $vaultData = null;

    public function __construct()
    {
        $this->vaultPath = config('vault.vault_path', storage_path('app/security/vault.json'));
        $this->masterKey = (string) config('vault.master_key', config('app.key', ''));
        $this->cipher = strtolower(config('vault.cipher', 'aes-256-gcm'));
        $this->activeKeyId = config('vault.active_key_id', 'pm-key-v1');
        $this->enabled = (bool) config('vault.enabled', true);
    }

    /**
     * Check if Question Vault encryption is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get the vault file path.
     */
    public function getVaultPath(): string
    {
        return $this->vaultPath;
    }

    /**
     * Initialize the vault.json file if it does not already exist.
     */
    public function initVault(?string $keyId = null): array
    {
        $dir = dirname($this->vaultPath);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0750, true);
        }

        $keyId = $keyId ?: $this->activeKeyId;
        $dekRaw = random_bytes(32); // 256-bit data encryption key
        $dekBase64 = base64_encode($dekRaw);

        $vaultContent = [
            'version' => '1.0',
            'cipher' => $this->cipher,
            'active_key_id' => $keyId,
            'keys' => [
                $keyId => [
                    'id' => $keyId,
                    'algorithm' => strtoupper($this->cipher),
                    'created_at' => now()->toIso8601String(),
                    'key_base64' => $dekBase64,
                ],
            ],
        ];

        File::put($this->vaultPath, json_encode($vaultContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->vaultData = $vaultContent;

        return $vaultContent;
    }

    /**
     * Rotate key by generating a new key ID and setting it as active.
     */
    public function rotateKey(string $newKeyId): array
    {
        $data = $this->loadVaultData();

        $dekRaw = random_bytes(32);
        $dekBase64 = base64_encode($dekRaw);

        $data['active_key_id'] = $newKeyId;
        $data['keys'][$newKeyId] = [
            'id' => $newKeyId,
            'algorithm' => strtoupper($this->cipher),
            'created_at' => now()->toIso8601String(),
            'key_base64' => $dekBase64,
        ];

        File::put($this->vaultPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->vaultData = $data;

        return $data;
    }

    /**
     * Encrypt plaintext into vault envelope string.
     * Format: vault:v1:{key_id}:{iv_b64}:{ciphertext_b64}:{tag_b64}
     */
    public function encrypt(?string $plaintext): ?string
    {
        if ($plaintext === null || $plaintext === '') {
            return $plaintext;
        }

        if (! $this->enabled) {
            return $plaintext;
        }

        // Avoid re-encrypting already encrypted payload
        if (str_starts_with($plaintext, 'vault:v1:')) {
            return $plaintext;
        }

        $vault = $this->loadVaultData();
        $keyId = $vault['active_key_id'] ?? $this->activeKeyId;
        $rawKey = $this->resolveRawKey($keyId, $vault);

        if ($this->cipher === 'aes-256-gcm') {
            $iv = random_bytes(12); // standard 96-bit IV for GCM
            $tag = '';
            $ciphertext = openssl_encrypt(
                $plaintext,
                'aes-256-gcm',
                $rawKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                '',
                16
            );

            if ($ciphertext === false) {
                throw new Exception('Vault encryption failed with AES-256-GCM.');
            }

            return sprintf(
                'vault:v1:%s:%s:%s:%s',
                $keyId,
                base64_encode($iv),
                base64_encode($ciphertext),
                base64_encode($tag)
            );
        }

        // Fallback AES-256-CBC
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $rawKey, OPENSSL_RAW_DATA, $iv);
        $tag = hash_hmac('sha256', $ciphertext, $rawKey, true);

        return sprintf(
            'vault:v1:%s:%s:%s:%s',
            $keyId,
            base64_encode($iv),
            base64_encode($ciphertext),
            base64_encode($tag)
        );
    }

    /**
     * Decrypt vault envelope string back to plaintext.
     */
    public function decrypt(?string $payload): ?string
    {
        if ($payload === null || $payload === '') {
            return $payload;
        }

        // If not vault format, return as is (supports legacy plaintext gracefully)
        if (! str_starts_with($payload, 'vault:v1:')) {
            return $payload;
        }

        $parts = explode(':', $payload);
        if (count($parts) < 6) {
            return $payload;
        }

        // format: vault : v1 : keyId : iv : ciphertext : tag
        $keyId = $parts[2];
        $iv = base64_decode($parts[3]);
        $ciphertext = base64_decode($parts[4]);
        $tag = base64_decode($parts[5]);

        $vault = $this->loadVaultData();
        $rawKey = $this->resolveRawKey($keyId, $vault);

        if ($this->cipher === 'aes-256-gcm') {
            $plaintext = openssl_decrypt(
                $ciphertext,
                'aes-256-gcm',
                $rawKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($plaintext === false) {
                throw new Exception("Vault decryption failed for key [{$keyId}]. Authentication tag mismatch or corrupted ciphertext.");
            }

            return $plaintext;
        }

        // Fallback CBC
        $expectedHmac = hash_hmac('sha256', $ciphertext, $rawKey, true);
        if (! hash_equals($expectedHmac, $tag)) {
            throw new Exception("Vault HMAC verification failed for key [{$keyId}].");
        }

        return openssl_decrypt($ciphertext, 'aes-256-cbc', $rawKey, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Check if a string is in vault ciphertext format.
     */
    public function isEncrypted(string $value): bool
    {
        return str_starts_with($value, 'vault:v1:');
    }

    /**
     * Load or initialize vault data.
     */
    protected function loadVaultData(): array
    {
        if ($this->vaultData !== null) {
            return $this->vaultData;
        }

        if (File::exists($this->vaultPath)) {
            $json = json_decode(File::get($this->vaultPath), true);
            if (is_array($json) && isset($json['keys'])) {
                $this->vaultData = $json;

                return $this->vaultData;
            }
        }

        // If vault file doesn't exist yet, auto-initialize
        return $this->initVault();
    }

    /**
     * Resolve raw 32-byte key from vault data or derived master key.
     */
    protected function resolveRawKey(string $keyId, array $vault): string
    {
        if (isset($vault['keys'][$keyId]['key_base64'])) {
            return base64_decode($vault['keys'][$keyId]['key_base64']);
        }

        // Fallback derivation from masterKey
        $cleanMasterKey = $this->masterKey;
        if (str_starts_with($cleanMasterKey, 'base64:')) {
            $cleanMasterKey = base64_decode(substr($cleanMasterKey, 7));
        }

        return hash('sha256', $cleanMasterKey.$keyId, true);
    }
}
