<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CandidateStudent;
use App\Models\GClass;
use App\Models\Subject;
use App\Models\BaseCalendar;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $fillable=['name'];
    /**
     * Get all of the candidates for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(CandidateStudent::class);
    }

    /**
     * Get all of the g_classes for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function g_classes(): HasMany
    {
        return $this->hasMany(GClass::class);
    }

    /**
     * Get all of the subjects for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get all of the base_calendars for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function base_calendars(): HasMany
    {
        return $this->hasMany(BaseCalendar::class);
    }

    /**
     * Get all of the students for the Grade
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getTeachersAttribute()
    {
        $teachers = collect();

        foreach ($this->subjects as $subject) {
            foreach ($subject->teachers as $teacher) {
                if (!$teachers->contains('id', $teacher->id)) {
                    $teachers->push($teacher);
                }
            }
        }
    return $teachers;
    }

    public function getSupervisorsAttribute()
    {
        $supervisors = collect();

        foreach ($this->g_classes as $class) {
            foreach ($class->supervisors as $supervisor) {
                if (!$supervisors->contains('id', $supervisor->id)){
                    $supervisors->push($supervisor);
                }
            }
        }

        return $supervisors;
    }
}
