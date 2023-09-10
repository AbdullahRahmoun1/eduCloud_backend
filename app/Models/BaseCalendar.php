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
    protected $hidden=['created_at','updated_at', 'grade'];
    protected $guarded =['created_at','updated_at'];
    protected $appends=['finished'];
    
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

    public function getFinishedAttribute(){
        
        $g_classes = $this->grade->g_classes->count();
        $done_classes = ProgressCalendar::where('base_calendar_id',$this->id)->count();
        return $done_classes >= $g_classes ? true : false;
    }
}
