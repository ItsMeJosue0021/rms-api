<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuscriptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'abstract' => $this->abstract,
            'school_year' => $this->school_year,
            'category' => $this->category,
            'keywords' => $this->keywords,
            'authors' => $this->authors,
            'program' => $this->program,
            'department' => $this->department,
            'files' => ManuscriptFileResource::collection($this->whenLoaded('files')),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}

