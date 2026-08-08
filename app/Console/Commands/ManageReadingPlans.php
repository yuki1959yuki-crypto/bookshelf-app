<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminder;
use Illuminate\Console\Command;

class ManageReadingPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manage-reading-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {

        ReadingPlan::where('status', ReadingPlanStatus::NotStarted)
            ->where('target_date', '<', now()->toDateString())
            ->update(['status' => ReadingPlanStatus::Expired]);

        $remindDate = now()->addDays(3)->toDateString();
        $plans = ReadingPlan::where('status', ReadingPlanStatus::NotStarted)
            ->where('target_date', $remindDate)
            ->get();

        foreach ($plans as $plan) {
            $plan->user->notify(new ReadingPlanReminder($plan));
        }
    }
}
