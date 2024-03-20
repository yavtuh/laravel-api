<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmHeaderAuth extends Model
{
    use HasFactory;

    protected $table = 'crm_header_auths';

    protected $fillable = [
        'crm_header_id',
        'base_url',
        'method',
        'token_path',
        'auth_type',
        'content_type',
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(CrmHeader::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CrmHeaderAuthField::class);
    }
}
