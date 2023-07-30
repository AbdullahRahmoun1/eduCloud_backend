<?php

namespace App\Models;

use App\Models\Grade;
use App\Models\AtMark;
use App\Models\Number;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidateStudent extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded = [];
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

    public function atMarks(): MorphMany
    {
        return $this->morphMany(AtMark::class, 'student');
    }
}
