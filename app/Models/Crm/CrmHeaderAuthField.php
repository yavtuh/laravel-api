<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmHeaderAuthField extends Model
{
    use HasFactory;

    protected $table = 'crm_header_auth_fields';

    protected $fillable = [
        'crm_header_auth_id',
        'header_name',
        'header_value',
        'header_type',
    ];

    public function auth(): BelongsTo
    {
        return $this->belongsTo(CrmHeaderAuth::class);
    }
}
