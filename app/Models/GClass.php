<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Test;
use App\Models\Employee;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class GClass extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at','pivot'];
    protected $fillable=[
    'grade_id',
    'name',
    'max_number',
];
    /**
     * Get the grade that owns the GClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get all of the students for the GClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get all of the tests for the GClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    /**
     * The supervisors that belong to the GClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'class_supervisor', 'g_class_id', 'employee_id');
    }

    /**
     * The teachers that belong to the GClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class,'class_teacher_subject','g_class_id', 'employee_id');
    }

    /**
     * The subjects that belong to the GClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_teacher_subject', 'g_class_id', 'subject_id');
    }
    
    public function getFreeSpaces():int{
        return $this->max_number-$this->students()->count();
    }

    //TODO: add progress calendar relation
}
