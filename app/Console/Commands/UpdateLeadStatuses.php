<?php

namespace App\Console\Commands;

use App\Models\Crm\Crm;
use App\Services\Crm\CrmStatusService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateLeadStatuses extends Command
{
    protected CrmStatusService $crmStatusService;
    public function __construct(CrmStatusService $crmStatusService)
    {
        $this->crmStatusService =  $crmStatusService;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:update-statuses';
    protected $description = 'Update lead statuses from all CRMs.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $crms = Crm::get();
        foreach ($crms as $crm){
            $this->crmStatusService->send($crm, Carbon::now()->subWeek(), Carbon::now());
        }
    }
}
