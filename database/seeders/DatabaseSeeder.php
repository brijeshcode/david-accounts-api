<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // production seeders
        // add user david and brijes data from seeder and rest user via user import option csv 
 
    }
}

//  php artisan tenants:artisan "migrate:fresh --seed" 
//  php artisan tenants:artisan "migrate"