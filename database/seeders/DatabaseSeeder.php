<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->has(Company::factory()->count(rand(0,10)),'companies')
            ->count(5)
            ->create();
    }
}
