<?php

namespace App\Repositories\Lead\Contracts;


use App\Models\Lead\Lead;

interface LeadRepositoryContract
{
    public function create(array $data): Lead|null;
    public function update(array $data, Lead|int $id): bool;
    public function updateAllByCrm(array $data, int $crmId, string $status): bool;
}
