<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmStatusField extends Model
{
    use HasFactory;

    protected $table = 'crm_status_fields';

    protected $fillable = [
        'crm_status_lead_id',
        'remote_field',
        'field_type',
        'another_value',
        'is_start_date',
        'is_end_date',
    ];

    public function getIsStartDateAttribute(): bool
    {
        return $this->attributes['is_start_date'] === 1;
    }

    public function getIsEndDateAttribute(): bool
    {
        return $this->attributes['is_end_date'] === 1;
    }

    public function statusLead(): BelongsTo
    {
        return $this->belongsTo(CrmStatusLead::class);
    }


}
