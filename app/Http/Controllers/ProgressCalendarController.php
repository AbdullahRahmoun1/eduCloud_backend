<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\BaseCalendar;
use App\Models\GClass;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
use App\Models\ProgressCalendar;
use Illuminate\Validation\Rule;

class ProgressCalendarController extends Controller
{
    public function addAchievement($abort = true){

        $data = request()->validate([
            'base_calendar_id' => ['required', 'exists:base_calendars,id'],
            'g_class_id' => ['required', 'exists:g_classes,id',
            Rule::unique('progress_calendars', 'g_class_id')
                ->where('base_calendar_id', request()->base_calendar_id)
            ]
        ],[
            'g_class_id.unique' => 'this goal is already achieved'
        ]);

        Helper::tryToEdit($data['g_class_id']);

        $base = BaseCalendar::find($data['base_calendar_id']);
        $g_class = GClass::find($data['g_class_id']);

        if($base->grade != $g_class->grade){
            res::error('this goal is not intended for this class.', code:422);
        }

        //TODO:Add the progress id to the corresponding test  
        $progress = Helper::lazyQueryTry(
            fn() => ProgressCalendar::create($data));
        
        if(!$abort){
            return $progress;
        }
        
        res::success('progress added successfully', $progress);
    }

    public function getProgressOfClass($g_class_id){
        
        Helper::tryToRead($g_class_id);
        
        $g_class = GClass::find($g_class_id);
        if(!$g_class){
            res::error('this class id is not valid',code:422);
        }

        $grade = $g_class->grade;
        $calendar = BaseCalendar::where('grade_id',$grade->id)
        ->orderBy('date')->get();

        $progress = ProgressCalendar::where('g_class_id', $g_class_id);
        $ids = $progress->pluck('base_calendar_id')->toArray();

        $calendar->map(function($goal) use ($ids){
            return in_array($goal->id, $ids) ?
            $goal->done = true :
            $goal->done = false;
        });
        
        res::success('calendar of this class retrieved successfully', $calendar);
    }
}
