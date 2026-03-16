<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->resource['message'] ?? null,
            'user' => isset($this->resource['user']) ? new UserResource($this->resource['user']) : null,
            'token' => $this->resource['token'] ?? null,
        ];
    }
}

