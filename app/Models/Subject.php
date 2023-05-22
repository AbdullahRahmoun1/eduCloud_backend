<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Subject extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];

    /**
     * Get all of the ability_tests for the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ability_tests(): HasMany
    {
        return $this->hasMany(AbilityTest::class);
    }

    /**
     * Get all of the base_calendar for the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function base_calendar(): HasMany
    {
        return $this->hasMany(BaseCalendar::class);
    }

    /**
     * Get all of the tests for the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    /**
     * The teachers that belong to the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'class_teacher_subject', 'subject_id', 'employee_id');
    }

    /**
     * The g_classes that belong to the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function g_classes(): BelongsToMany
    {
        return $this->belongsToMany(GClass::class, 'class_teacher_subject', 'subject_id', 'g_class_id');
    }

    /**
     * Get the grade that owns the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }
}
