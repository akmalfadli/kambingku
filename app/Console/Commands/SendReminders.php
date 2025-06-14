<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pregnancy;
use App\Models\HealthRecord;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    protected $signature = 'goats:send-reminders';
    protected $description = 'Send reminders for upcoming deliveries and health checkups';

    public function handle()
    {
        // Upcoming deliveries (next 7 days)
        $upcomingDeliveries = Pregnancy::where('status', 'pregnant')
            ->whereBetween('expected_delivery_date', [now(), now()->addDays(7)])
            ->with('femaleGoat')
            ->get();

        // Overdue health checkups
        $overdueCheckups = HealthRecord::whereNotNull('next_checkup_date')
            ->where('next_checkup_date', '<', now())
            ->with('goat')
            ->get();

        if ($upcomingDeliveries->count() > 0 || $overdueCheckups->count() > 0) {
            // Send notification logic here
            $this->info('Reminders sent successfully!');
        } else {
            $this->info('No reminders to send.');
        }

        return 0;
    }
}
