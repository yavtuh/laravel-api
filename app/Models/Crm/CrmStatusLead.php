<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmStatusLead extends Model
{
    use HasFactory;

    protected $table = 'crm_status_leads';

    protected $fillable = [
        'crm_id',
        'base_url',
        'method',
        'content_type',
        'path_leads',
        'path_uuid',
        'path_status',
        'local_field',
    ];

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CrmStatusField::class);
    }
}
