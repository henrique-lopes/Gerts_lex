<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;
use Laravel\Cashier\Billable;

class Tenant extends BaseTenant
{
    use Billable;

    protected $fillable = [
        'name',
        'domain',
        'database',
        'subscription_status',
        'subscription_plan',
        'trial_ends_at',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get the database name for this tenant
     */
    public function getDatabaseName(): string
    {
        return $this->database ?? 'tenant_' . $this->id;
    }

    /**
     * Check if tenant is on trial
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active';
    }

    /**
     * Check if tenant can access the application
     */
    public function canAccess(): bool
    {
        return $this->hasActiveSubscription() || $this->isOnTrial();
    }
}

