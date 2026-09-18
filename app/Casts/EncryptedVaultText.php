<?php

namespace App\Casts;

use App\Services\Security\QuestionVaultService;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class EncryptedVaultText implements CastsAttributes
{
    protected QuestionVaultService $vaultService;

    public function __construct()
    {
        $this->vaultService = app(QuestionVaultService::class);
    }

    /**
     * Cast the given value from the database (decrypt).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        return $this->vaultService->decrypt((string) $value);
    }

    /**
     * Prepare the given value for storage in the database (encrypt).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        return $this->vaultService->encrypt((string) $value);
    }
}
