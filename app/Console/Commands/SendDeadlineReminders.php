<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deadline;
use App\Notifications\DeadlineReminder;
use Carbon\Carbon;
use Spatie\Multitenancy\Models\Tenant;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembretes para prazos que vencem em breve.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Make sure the tenant's database is connected
            $tenant->makeCurrent();

            $deadlines = Deadline::where('due_date', '>', Carbon::now())
                                 ->where('due_date', '<=', Carbon::now()->addDays(3))
                                 ->where('status', 'pending')
                                 ->get();

            foreach ($deadlines as $deadline) {
                // Assuming the user associated with the deadline should receive the notification
                // For simplicity, let's notify the first user of the tenant
                $user = $tenant->users()->first(); 

                if ($user) {
                    $user->notify(new DeadlineReminder($deadline));
                    $this->info("Lembrete enviado para o prazo: {$deadline->title} (Tenant: {$tenant->name})");
                }
            }

            $tenant->forgetCurrent();
        }

        $this->info('Lembretes de prazos enviados com sucesso!');
    }
}
