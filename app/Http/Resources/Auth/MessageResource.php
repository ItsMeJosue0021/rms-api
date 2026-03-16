<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function __construct(string $message)
    {
        parent::__construct(['message' => $message]);
    }

    public function toArray(Request $request): array
    {
        return [
            'message' => $this->resource['message'],
        ];
    }
}

