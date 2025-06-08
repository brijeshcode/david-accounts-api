<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait SetsTenantConnection
{
    public function setTenantConnection(): void
    {
        DB::setDefaultConnection('tenant');
    }
}
