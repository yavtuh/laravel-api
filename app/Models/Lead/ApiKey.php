<?php

namespace App\Models\Lead;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $table = 'api_keys';

    protected $fillable = [
        'key',
        'description',
        'active'
    ];

    public function getActiveAttribute(): bool
    {
        return $this->attributes['active'] === 1;
    }

    public function scopeActive($query) {
        return $query->where('active', true);
    }
}
