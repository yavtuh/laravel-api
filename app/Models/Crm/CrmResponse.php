<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmResponse extends Model
{
    use HasFactory;

    protected $table = 'crm_responses';

    protected $fillable = [
        'crm_id',
        'response_type',
        'response_path',
        'expected_value',
        'is_empty',
        'expected_type',
    ];

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }

    public function getIsEmptyAttribute(): bool
    {
        return $this->attributes['is_empty'] === 1;
    }
}
