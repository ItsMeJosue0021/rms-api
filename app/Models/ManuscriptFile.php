<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManuscriptFile extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    use HasUuid;

    protected $fillable = [
        'manuscript_id',
        'file_type',
        'path',
    ];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }
}

