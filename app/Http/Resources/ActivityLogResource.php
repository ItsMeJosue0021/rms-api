<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'actor' => [
                'type' => $this->actor_type,
                'id' => $this->actor_id,
                'name' => $this->whenLoaded('actor', fn () => $this->actor->name),
                'email' => $this->whenLoaded('actor', fn () => $this->actor->email),
                'role' => $this->whenLoaded('actor', fn () => $this->actor->role),
            ],
            'target' => [
                'type' => $this->target_type,
                'id' => $this->target_id,
            ],
            'metadata' => $this->metadata,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

