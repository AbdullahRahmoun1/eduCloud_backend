<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorOfBus extends Model
{
    use HasFactory;
    protected $table='supervisor_of_bus';
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
}
