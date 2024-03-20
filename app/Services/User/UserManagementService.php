<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\User\Contracts\UserManagementServiceContract;

class UserManagementService implements UserManagementServiceContract
{

    public function findByKey(array $data): User|null
    {
        if(isset($data['utm'])){
            $utmData = [];
            parse_str($data['utm'], $utmData);
            if (isset($utmData['utm_campaign'])) {
                $utmCampaign = $utmData['utm_campaign'];
                return User::where('key', $utmCampaign)->first();
            }
        }

        if (isset($data['key'])){
            return User::where('key', $data['key'])->first();
        }

        return null;
    }
}
