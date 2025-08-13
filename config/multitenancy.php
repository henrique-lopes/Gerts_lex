<?php

return [
    /*
     * This class is responsible for determining which tenant should be current
     * for the given request.
     *
     * This class should extend `Spatie\Multitenancy\TenantFinder\TenantFinder`
     *
     */
    'tenant_finder' => \Spatie\Multitenancy\TenantFinder\DomainTenantFinder::class,

    /*
     * These fields are used by tenant:artisan command to match one or more tenant
     */
    'tenant_artisan_search_fields' => [
        'id',
        'name',
        'domain',
    ],

    /*
     * These tasks will be performed when switching tenants.
     *
     * A valid task is any class that implements Spatie\Multitenancy\Tasks\SwitchTenantTask
     */
    'switch_tenant_tasks' => [
        // \Spatie\Multitenancy\Tasks\PrefixCacheTask::class,
        // \Spatie\Multitenancy\Tasks\SwitchTenantDatabaseTask::class,
    ],

    /*
     * This class is the model used for storing configuration on tenants.
     *
     * It must be or extend `Spatie\Multitenancy\Models\Tenant::class`
     */
    'tenant_model' => \Spatie\Multitenancy\Models\Tenant::class,

    /*
     * If there is a current tenant when dispatching a job, the id of the current tenant
     * will be automatically set on the job. When the job is executed, the set tenant on the job
     * will be made current.
     */
    'queues_are_tenant_aware_by_default' => true,

    /*
     * The connection name to reach the tenant database.
     *
     * Set to `null` to use the default connection.
     */
    'tenant_database_connection_name' => null,

    /*
     * The connection name to reach the landlord database.
     */
    'landlord_database_connection_name' => null,

    /*
     * This key will be used to bind the current tenant in the container.
     */
    'current_tenant_container_key' => 'currentTenant',

    /*
     * Set it to `true` if you like to cache the tenant(s) routes.
     * This is needed in some cases for performance reasons.
     */
    'cache_tenant_routes' => env('CACHE_TENANT_ROUTES', false),
];

