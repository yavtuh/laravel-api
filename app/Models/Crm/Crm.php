<?php

namespace App\Models\Crm;

use App\Models\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Crm extends Model
{
    use HasFactory;

    protected $table = 'crms';

    protected $fillable = [
        'name',
        'description',
    ];

    public function headers(): HasMany
    {
        return $this->hasMany(CrmHeader::class);
    }

    public function createLead(): HasOne
    {
        return $this->hasOne(CrmCreateLead::class);
    }

    public function statusLead(): HasOne
    {
        return $this->hasOne(CrmStatusLead::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(CrmSetting::class);
    }

    public function responses() :HasMany
    {
        return $this->hasMany(CrmResponse::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function isActive(): bool
    {
        return $this->settings->is_active;
    }

    public function isFilledCapToday() : bool
    {
        $cap = $this->settings->daily_cap;
        $leadCount = $this->leads()->whereDate('created_at', Carbon::today())->where('send_status', 'sent')->count();
        return $leadCount >= $cap;
    }

    public function nextSendTime()
    {
        $nextWorkingTime = $this->nextWorkingTime();
        $lastDeferredLead = $this->leads()->where('send_status', 'deferred')->orderByDesc('send_date')->first();
        if($lastDeferredLead){
            $randomSeconds = rand(180, 300);
            $sendDate = Carbon::parse($lastDeferredLead->send_date)->addSeconds($randomSeconds);
            if($sendDate->gt($nextWorkingTime)){
                return $sendDate;
            }
        }
        return $nextWorkingTime;
    }

    public function nextWorkingTime()
    {
        $now = Carbon::now();
        $nextWorkingPeriodStart = $now->copy();

        $workingDays = $this->settings->working_days;
        $workingHoursStart = $this->settings->working_hours_start;
        $workingHoursEnd = $this->settings->working_hours_end;

        $startHours = $workingHoursStart->hour;
        $startMinutes = $workingHoursStart->minute;

        if ($now->lt($workingHoursStart) && in_array($now->dayOfWeek, $workingDays)) {
            return $nextWorkingPeriodStart->setTime($startHours, $startMinutes);
        }

        if ($now->gt($workingHoursEnd)) {
            $nextWorkingPeriodStart->addDay();
        }

        while (!in_array($nextWorkingPeriodStart->dayOfWeek, $workingDays)) {
            $nextWorkingPeriodStart->addDay();
        }

        return $nextWorkingPeriodStart->setTime($startHours, $startMinutes);
    }

}
