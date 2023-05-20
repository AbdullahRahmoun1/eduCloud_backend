<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AbilityTest;
use App\Models\Account;
use App\Models\AtMark;
use App\Models\AtSection;
use App\Models\Number;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Account::create([
            'password'=>'12345',
            'user_name'=>'admin',
            'owner_id'=>'101010101',
            'owner_type'=>'the best of the best',
        ]);
        Number::factory(100)->create();
        AbilityTest::factory(50)->create();
        AtSection::factory(100)->create();
        AtMark::factory(300)->create();
    }
}
