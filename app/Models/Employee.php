<?php

namespace App\Models;

use App\Models\GClass;
use App\Models\Number;
use App\Models\Account;
use App\Models\Subject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory,HasRoles;
    protected $hidden=['created_at','updated_at'];
    protected $fillable = ['first_name', 'last_name'];
    protected $guard_name = 'web';
    public function account(){
        return $this->morphOne(Account::class,'owner');
    }
    public function user(){
        return $this->morphOne(Account::class,'owner');
    }
    public function number(){
        return $this->morphOne(Number::class,'owner');
    }

    /**
     * The g_classes_sup that belong to the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function g_classes_sup(): BelongsToMany
    {
        return $this->belongsToMany(GClass::class, 'class_supervisor', 'employee_id', 'g_class_id');
    }

    /**
     * The subjects that belong to the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_teacher_subject', 'employee_id', 'subject_id');
    }

    /**
     * The g_classes that belong to the Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function g_classes_teacher(): BelongsToMany
    {
        return $this->belongsToMany(GClass::class, 'class_teacher_subject', 'employee_id', 'g_class_id');
    }
}
