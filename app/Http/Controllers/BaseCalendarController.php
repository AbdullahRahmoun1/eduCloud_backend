<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
use App\Models\BaseCalendar;
use Illuminate\Validation\Rule;

class BaseCalendarController extends Controller
{
    public function add(Request $request){

        $data = $this->validateCalendar($request);

        $calendar =  Helper::lazyQueryTry(fn()=>BaseCalendar::create($data));
    
        res::success('plan created successfully', $calendar);
    }

    public function edit(Request $request, $calendar_id){

        $data = $this->validateCalendar($request, $calendar_id);

        $calendar = BaseCalendar::find($calendar_id);

        if(!$calendar){
            res::error('this base calendar id is not valid',code:422);
        }

        Helper::lazyQueryTry(fn()=>$calendar->update($data));
    
        res::success('plan updated successfully', $calendar);
    }

    public function validateCalendar($request, $calendar_id = null){

        $data = $request->validate([
            'subject_id' => [
                'required',
                'exists:subjects,id',
                Rule::unique('base_calendars')->ignore($calendar_id)->where(function ($query) use ($request) {
                    return $query->where('grade_id', $request->grade_id)
                        ->where('title', $request->title);
                }),
            ],
            'grade_id' => 'required|exists:grades,id',
            'title' => 'required|min:1|max:30',
            'date' => 'required|date',
            'is_test' => 'required|boolean'
        ],[
            'subject_id.unique' => 'this title already exists with this subject and class'
        ]);
    
        $grade = Grade::find($data['grade_id']);
        $subject = Subject::find($data['subject_id']);
        
        if (!$grade->subjects->contains($subject)) {
            res::error('The subject does not belong to the specified grade', code:422);
        }

        return $data;
    }

    //---------------------------------------------------------

    public function getCalendarOfSubject($subject_id){

        if(!Subject::find($subject_id)){
            res::error('this subject id is not valid', code:422);
        }

        $calendars = BaseCalendar::where('subject_id', $subject_id)
            ->orderBy('date', 'asc')
            ->get();
            
            return res::success('calendar retrieved successfully', $calendars);
        }
}
