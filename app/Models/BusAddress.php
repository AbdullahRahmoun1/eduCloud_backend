<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusAddress extends Model
{
    use HasFactory;
    protected $table='bus_address';
    public static function joins() {
        return DB::table('bus_address as ba')
        ->join('buses as b','b.id','=','ba.bus_id')
        ->join('addresses as a','a.id','=','ba.address_id');
    }
}
