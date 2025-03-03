<?php

namespace Modules\Auth\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefreshTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'refresh_token' => $this->plainTextToken,
            'refresh_token_expires_at' => $this->accessToken->expires_at ?? $this->token_expires_at,
        ];
    }
}
