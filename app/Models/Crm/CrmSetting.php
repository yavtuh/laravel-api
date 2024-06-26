<?php

namespace App\Models\Crm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmSetting extends Model
{
    use HasFactory;

    protected $table = 'crm_settings';

    protected $fillable = [
        'crm_id',
        'working_hours_start',
        'working_hours_end',
        'working_days',
        'daily_cap',
        'is_active',
        'skip_after_workings',
        'generate_email_if_missing',
    ];

    protected $casts = [
        'working_hours_start' => 'datetime:H:i',
        'working_hours_end' => 'datetime:H:i',
        'working_days' => 'array',
    ];

    public function crm(): BelongsTo
    {
        return $this->belongsTo(Crm::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->attributes['is_active'] === 1;
    }

    public function getSkipAfterWorkingsAttribute(): bool
    {
        return $this->attributes['skip_after_workings'] === 1;
    }

    public function getGenerateEmailIfMissingAttribute(): bool
    {
        return $this->attributes['generate_email_if_missing'] === 1;
    }

    public function isWorkingTime(): bool
    {
        $now = Carbon::now();
        $currentDay = $now->dayOfWeek;
        $currentTime = $now->format('H:i');

        return in_array($currentDay, $this->working_days) &&
            $currentTime >= $this->working_hours_start->format('H:i') &&
            $currentTime <= $this->working_hours_end->format('H:i');
    }

    public function isFilledCapToday() : bool
    {
        $cap = $this->daily_cap;
        $leadCount = $this->crm->leads()->whereDate('created_at', Carbon::today())->where('send_status', 'sent')->count();
        return $leadCount >= $cap;
    }

}
