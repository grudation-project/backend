<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Auth\Exceptions\RefreshTokenException;
use Modules\Auth\Models\RefreshToken;

class RefreshTokenService
{
    public function rotate(array $data)
    {
        $refreshToken = RefreshToken::query()
            ->where('user_id', auth()->id())->where('token', $this->getEncryptedToken($data['token']))
            ->firstOrFail();

        $this->assertExpired($refreshToken);

        return DB::transaction(function () use ($refreshToken) {
            $refreshToken->delete();

            return $this->store();
        });
    }

    public function store($userId = null)
    {
        $userId = $userId ?: auth()->id();

        $plainTextToken = Str::random(50);
        $refreshToken = RefreshToken::create([
            'token' => $this->getEncryptedToken($plainTextToken),
            'user_id' => $userId,
            'token_expires_at' => now()->addDays(5),
        ]);

        $refreshToken->plainTextToken = $plainTextToken;

        return $refreshToken;
    }

    public function getEncryptedToken(string $plainTextToken): string
    {
        return hash('sha256', $plainTextToken);
    }

    public function assertExpired(RefreshToken $refreshToken)
    {
        if ($refreshToken->token_expires_at->isPast()) {
            RefreshTokenException::expired();
        }
    }
}
