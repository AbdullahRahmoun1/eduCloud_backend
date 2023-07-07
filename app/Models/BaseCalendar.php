<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BaseCalendar extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];

    /**
     * Get the grade that owns the BaseCalendar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the subject that owns the BaseCalendar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
