<?php

namespace App\Models;

use App\Models\Student;
use App\Models\MoneySubRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoneyRequest extends Model
{
    use HasFactory;
    public const SCHOOL='school',BUS='bus';
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
    protected $guarded =['created_at','updated_at'];
    
    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function moneySubRequests(){
        return $this->hasMany(MoneySubRequest::class);
    }
}
