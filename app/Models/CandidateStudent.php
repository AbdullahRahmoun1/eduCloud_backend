<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;
use App\Models\Number;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CandidateStudent extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $fillable = [
        'grade_id',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'place_of_living',
        'birth_date',
        '6th_grade_avg',
        'rejected',
        'reason',
    ];
    /**
     * Get the grade that owns the CandidateStudent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'owner');
    }

    public function numbers(): MorphMany
    {
        return $this->morphMany(Number::class, 'owner');
    }
}
