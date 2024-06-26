<?php

namespace App\Services\Funnel;

use App\Models\Funnel\Funnel;

class FunnelManagementService implements Contracts\FunnelManagementServiceContract
{

    public function findByName(array $data): Funnel|null
    {
        if(isset($data['funnel'])){
            return Funnel::where('name', $data['funnel'])->first();
        }

        return null;
    }
}
