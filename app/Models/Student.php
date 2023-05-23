<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];

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

    public function numbers(): MorphMany
    {
        return $this->morphMany(Number::class,'owner');
    }

}