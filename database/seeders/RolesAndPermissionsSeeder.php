<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles=[
            'admin',
            'principal',
            'secretary',
            'supervisor',
            'teacher',
            'student',
            'busAdmin',
            'accountant',
            'kioskAdmin',
            'guest',
        ];
        array_map(fn($role)=>Role::create(['name'=>$role]),$roles);
        
    }
}
