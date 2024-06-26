<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmCreateField extends Model
{
    use HasFactory;

    protected $table = 'crm_create_fields';

    protected $fillable = [
        'crm_create_lead_id',
        'local_field',
        'remote_field',
        'field_type',
        'another_value',
        'is_required',
        'is_random',
    ];

    public function getIsRequiredAttribute(): bool
    {
        return $this->attributes['is_required'] === 1;
    }

    public function getIsRandomAttribute(): bool
    {
        return $this->attributes['is_random'] === 1;
    }

    public function createLead(): BelongsTo
    {
        return $this->belongsTo(CrmCreateLead::class);
    }

}
