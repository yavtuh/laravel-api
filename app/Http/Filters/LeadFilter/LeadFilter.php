<?php

namespace App\Http\Filters\LeadFilter;

use App\Http\Filters\AbstractFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LeadFilter extends AbstractFilter
{
    public const SEARCH = 'search';
    public const STARTDATE = 'startDate';
    public const ENDDATE = 'endDate';
    public const SENDSTARTDATE = 'sendStartDate';
    public const SENDENDDATE = 'sendEndDate';
    public const FUNNEL = 'funnel';
    public const BUYER = 'buyer';
    public const LEADSTATUS = 'leadStatus';
    public const SENTSTATUS = 'sentStatus';

    public const DOMAIN = 'domain';
    public const CRM = 'crm';

    protected function getCallbacks(): array
    {
        return [
            self::SEARCH => [$this, 'search'],
            self::STARTDATE => [$this, 'startDate'],
            self::ENDDATE => [$this, 'endDate'],
            self::SENDSTARTDATE => [$this, 'sendStartDate'],
            self::SENDENDDATE => [$this, 'sendEndDate'],
            self::FUNNEL => [$this, 'funnel'],
            self::BUYER => [$this, 'buyer'],
            self::LEADSTATUS => [$this, 'leadStatus'],
            self::SENTSTATUS => [$this, 'sentStatus'],
            self::DOMAIN => [$this, 'domain'],
            self::CRM => [$this, 'crm'],
        ];
    }

    public function search(Builder $builder, $value){
        return $builder->where('first_name', 'LIKE', "%{$value}%")
            ->orWhere('last_name', 'LIKE', "%{$value}%")
            ->orWhere('phone', 'LIKE', "%{$value}%")
            ->orWhere('email', 'LIKE', "%{$value}%");
    }

    public function startDate(Builder $builder, $value){
        return $builder->whereDate('created_at', '>=', Carbon::parse($value));
    }

    public function endDate(Builder $builder, $value){
        return $builder->whereDate('created_at', '<=', Carbon::parse($value));
    }

    public function sendStartDate(Builder $builder, $value){
        return $builder->whereDate('send_date', '>=', Carbon::parse($value));
    }

    public function sendEndDate(Builder $builder, $value){
        return $builder->whereDate('send_date', '<=', Carbon::parse($value));
    }

    public function funnel(Builder $builder, $value){
        return $builder->where('funnel_id', $value);
    }

    public function buyer(Builder $builder, $value){
        return $builder->where('user_id', $value);
    }

    public function leadStatus(Builder $builder, $value){
        if($value === 'Неопределённый'){
            return $builder->whereNull('lead_status');
        }
        return $builder->where('lead_status', $value);
    }

    public function sentStatus(Builder $builder, $value){
        if($value === 'Неопределённый'){
            return $builder->whereNull('send_status');
        }
        return $builder->where('send_status', $value);
    }

    public function domain(Builder $builder, $value){
        return $builder->where('domain', $value);
    }

    public function crm(Builder $builder, $value){
        return $builder->where('crm_id', $value);
    }
}
