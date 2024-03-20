<?php

namespace App\Models\Funnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funnel extends Model
{
    use HasFactory;

    protected $table = 'funnels';

    protected $fillable = [
        'name',
        'description',
    ];

    public function settings(): HasMany
    {
        return $this->hasMany(FunnelSetting::class);
    }
}
