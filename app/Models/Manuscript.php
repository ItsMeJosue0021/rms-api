<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manuscript extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    use HasUuid;

    protected $fillable = [
        'title',
        'abstract',
        'school_year',
        'category',
        'keywords',
        'authors',
        'program',
        'department',
        'is_public',
    ];

    protected $casts = [
        'keywords' => 'array',
        'authors' => 'array',
        'is_public' => 'boolean',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(ManuscriptFile::class);
    }
}
