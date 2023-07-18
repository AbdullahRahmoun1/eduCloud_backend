<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
    protected $guarded = [];
    public function driver_numbers() {
        return $this->morphMany(Number::class,'owner');
    }
    /**
     * The addresses that belong to the Bus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addresses(){
        return $this->belongsToMany(Address::class, 'bus_address','bus_id','address_id')
        ->withPivot('price');
    }

    public function students(){
        return $this->belongsToMany(Student::class, 'student_bus','bus_id','student_id');
    }

    public function supervisors(){
        return $this->belongsToMany(Employee::class, 'supervisor_of_bus','bus_id','employee_id');
    }

}

