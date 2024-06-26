<?php

namespace App\Services\User\Contracts;

use App\Models\User;

interface UserManagementServiceContract
{
    public function findByKey(array $data): User|null;
}
