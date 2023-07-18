<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
    public function buses(){
        return $this->belongsToMany(Bus::class, 'bus_address','address_id','bus_id')
        ->withPivot('price');
    }
    /**
     * Get all of the students for the Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(){
        return $this->hasMany(Student::class);
    }
}
