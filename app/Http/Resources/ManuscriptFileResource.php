<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ManuscriptFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'file_type' => $this->file_type,
            'path' => $this->path,
            'url' => Storage::disk('public')->url($this->path),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
