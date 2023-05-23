<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Employee extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];

    //TODO: Morph relations

    public function account()
    {
        return $this->morphOne(Account::class,'owner');
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
        if($this->account())
        return $this->belongsToMany(GClass::class, 'class_teacher_subject', 'employee_id', 'g_class_id');
    }
}
