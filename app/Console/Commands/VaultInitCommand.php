<?php

namespace App\Console\Commands;

use App\Services\Security\QuestionVaultService;
use Illuminate\Console\Command;

class VaultInitCommand extends Command
{
    protected $signature = 'vault:init {--key-id= : Custom initial key ID}';

    protected $description = 'Initialize the Question Bank encryption vault (vault.json)';

    public function handle(QuestionVaultService $vaultService): int
    {
        $this->info('Initializing Question Bank encryption vault...');

        $keyId = $this->option('key-id') ?: config('vault.active_key_id', 'pm-key-v1');
        $vaultData = $vaultService->initVault($keyId);

        $this->info("Vault successfully created at: [{$vaultService->getVaultPath()}]");
        $this->info("Active Key ID: [{$vaultData['active_key_id']}]");
        $this->info("Cipher: [{$vaultData['cipher']}]");

        return self::SUCCESS;
    }
}
