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
}
