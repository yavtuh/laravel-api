<?php

namespace App\Console\Commands;

use App\Jobs\SendLeadToCrmJob;
use App\Models\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPendingLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:send-pending-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deffered lead';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leads = Lead::where('send_status', 'deferred')->whereDate('send_date', '<', Carbon::now())->get();
        foreach ($leads as $lead){
            SendLeadToCrmJob::dispatch($lead->id);
        }
    }
}
