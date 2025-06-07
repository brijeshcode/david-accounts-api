<?php
namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantDatabaseTask;

class SwitchTenantAndSetDefaultConnectionTask extends SwitchTenantDatabaseTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        parent::makeCurrent($tenant);

        // ✅ Set the tenant connection as default for all DB operations (even in CLI)
        config(['database.default' => 'tenant']);
    }

    public function forgetCurrent(): void
    {
        parent::forgetCurrent();

        // Optional: reset default to landlord
        config(['database.default' => 'landlord']);
    }
}
