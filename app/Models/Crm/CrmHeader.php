<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CrmHeader extends Model
{
    use HasFactory;

    protected $table = 'crm_headers';

    protected $fillable = [
        'crm_id',
        'header_name',
        'header_value',
    ];

    public function auth(): HasOne
    {
        return $this->hasOne(CrmHeaderAuth::class);
    }

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }
}
