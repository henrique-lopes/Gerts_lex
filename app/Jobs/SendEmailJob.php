<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantId;
    public $to;
    public $subject;
    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct(int $tenantId, string $to, string $subject, string $message)
    {
        $this->tenantId = $tenantId;
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Simple log without tenant context for testing
            Log::info("Processing email job", [
                'tenant_id' => $this->tenantId,
                'to' => $this->to,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);

            Log::info("Email job processed successfully");

        } catch (\Exception $e) {
            Log::error("Failed to process email job", [
                'error' => $e->getMessage(),
                'tenant_id' => $this->tenantId,
                'to' => $this->to,
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $tenant = Tenant::find($this->tenantId);
        $tenantName = $tenant ? $tenant->name : "Unknown (ID: {$this->tenantId})";
        
        Log::error("SendEmailJob failed for tenant: {$tenantName}", [
            'error' => $exception->getMessage(),
            'tenant_id' => $this->tenantId,
            'to' => $this->to,
        ]);
    }
}
