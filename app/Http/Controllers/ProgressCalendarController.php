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
}
