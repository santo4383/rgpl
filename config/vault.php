<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Question Vault Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration manages encryption of sensitive Question Bank data.
    | Data Encryption Keys (DEKs) are stored inside the vault.json file,
    | which is protected by the QUESTION_VAULT_MASTER_KEY.
    |
    */

    'enabled' => env('QUESTION_VAULT_ENABLED', true),

    'vault_path' => env('QUESTION_VAULT_PATH', storage_path('app/security/vault.json')),

    'master_key' => env('QUESTION_VAULT_MASTER_KEY', env('APP_KEY')),

    'cipher' => env('QUESTION_VAULT_CIPHER', 'aes-256-gcm'),

    'active_key_id' => env('QUESTION_VAULT_ACTIVE_KEY_ID', 'pm-key-v1'),
];
