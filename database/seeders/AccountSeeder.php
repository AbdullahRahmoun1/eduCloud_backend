<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Student;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account=
        Account::create([
            'password'=>'12345',
            'user_name'=>'admin',
            'owner_id'=>'1',
            'owner_type'=>Employee::class,
        ]);
        $account->assignRole(config('roles.admin'));
        $account=
        Account::create([
            'password'=>'12345',
            'user_name'=>'moajeh',
            'owner_id'=>'2',
            'owner_type'=>Employee::class,
        ]);
        $account->assignRole(config('roles.supervisor'));
        $account=
        Account::create([
            'password'=>'12345',
            'user_name'=>'student',
            'owner_id'=>'2',
            'owner_type'=>Student::class,
        ]);
        $account->assignRole(config('roles.student'));
        
    }
}
