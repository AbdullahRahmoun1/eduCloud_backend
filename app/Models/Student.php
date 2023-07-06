<?php

namespace App\Models;

use App\Models\Mark;
use App\Models\GClass;
use App\Models\Number;
use App\Models\Absence;
use App\Models\Account;
use App\Models\MoneyRequest;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $fillable = [
        'g_class_id',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'place_of_living',
        'birth_date',
        'birth_place',
        '6th_grade_avg',
        'social_description',
        'grand_father_name',
        'mother_last_name',
        'public_record',
        'father_alive',
        'mother_alive',
        'father_profession',
        'previous_school',
        'address_id',
        'transportation_subscriber',
        'registration_place',
        'registration_number',
        'registration_date',
        'notes',
    ];

    /**
     * Get all of the marks for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get the g_class that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function g_class(): BelongsTo
    {
        return $this->belongsTo(GClass::class);
    }
    
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class,'owner');
    }

    public function account(): MorphOne
    {
        return $this->morphOne(Account::class,'owner');
    }
    public function user(): MorphOne
    {
        return $this->morphOne(Account::class,'owner');
    }

    public function numbers(): MorphMany
    {
        return $this->morphMany(Number::class,'owner');
    }
    
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function moneyRequests(): HasMany{
        return $this->hasMany(MoneyRequest::class);
    }

}