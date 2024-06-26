<?php

namespace App\Models\Funnel;

use App\Models\Crm\Crm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelSetting extends Model
{
    use HasFactory;

    protected $table = 'funnel_settings';

    protected $fillable = [
        'funnel_id',
        'crm_id',
        'score',
    ];

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }
}
