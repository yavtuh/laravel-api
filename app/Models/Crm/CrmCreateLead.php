<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmCreateLead extends Model
{
    use HasFactory;

    protected $table = 'crm_create_leads';

    protected $fillable = [
        'crm_id',
        'base_url',
        'method',
        'content_type',
        'uuid_path',
    ];

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CrmCreateField::class);
    }

}
