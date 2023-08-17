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
        $emp=Employee::create([
            'first_name'=>'malath',
            'last_name'=>'_principal',
        ]);
        $emp->assignRole(config('roles.principal'));
        Account::create([
            'password'=>'12345',
            'user_name'=>'principal',
            'owner_id'=>$emp->id,
            'owner_type'=>Employee::class,
        ]);

        $emp=Employee::create([
            'first_name'=>'daudosh',
            'last_name'=>'_secretary',
        ]);
        $emp->assignRole(config('roles.secretary'));
        Account::create([
            'password'=>'12345',
            'user_name'=>'secretary',
            'owner_id'=>$emp->id,
            'owner_type'=>Employee::class,
        ]);

        $emp=Employee::create([
            'first_name'=>'ahmad',
            'last_name'=>'_supervisor',
        ]);
        $emp->assignRole(config('roles.supervisor'));
        Account::create([
            'password'=>'12345',
            'user_name'=>'supervisor',
            'owner_id'=>$emp->id,
            'owner_type'=>Employee::class,
        ]);

        $emp=Employee::create([
            'first_name'=>'busss',
            'last_name'=>'bus_supervisor',
        ]);
        $emp->assignRole(config('roles.busSupervisor'));
        Account::create([
            'password'=>'12345',
            'user_name'=>'kh',
            'owner_id'=>$emp->id,
            'owner_type'=>Employee::class,
        ]);
        
        Account::create([
            'password'=>'12345',
            'user_name'=>'student',
            'owner_id'=>1,
            'owner_type'=>Student::class,
        ]);
    }
}
