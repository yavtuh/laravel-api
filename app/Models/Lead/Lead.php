<?php

namespace App\Models\Lead;

use App\Models\Crm\Crm;
use App\Models\Funnel\Funnel;
use App\Models\Traits\Filterable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory, Filterable;

    protected $table = 'leads';

    protected $fillable = [
        'user_id',
        'crm_id',
        'funnel_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'utm',
        'user_agent',
        'ip',
        'extra',
        'domain',
        'country',
        'send_status',
        'lead_status',
        'response',
        'send_result',
        'uuid',
        'send_date',
        'sent_crms',
    ];

    protected $casts = [
        'sent_crms' => 'array',
    ];

    protected $dates = [
        'send_date',
    ];

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

}
