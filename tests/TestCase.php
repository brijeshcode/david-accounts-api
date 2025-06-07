<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;  

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    //

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        if (app()->environment('testing')) {
            // 👇 Use SQLite memory for landlord too
            config()->set('database.connections.landlord', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);

            config()->set('database.connections.tenant', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);

            Artisan::call('migrate', [
                '--database' => 'landlord',
                '--path' => '/database/migrations/landlord',
            ]);

            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => '/database/migrations',
            ]);
        }
    }

}
