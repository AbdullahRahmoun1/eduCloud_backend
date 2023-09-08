<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
use App\Models\BaseCalendar;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class BaseCalendarController extends Controller
{
    public function add(Request $request){

        $data = $this->validateCalendar($request);

        DB::beginTransaction();

        $result = [];
        $entryNum = 1;
        foreach($data as $entry){

            //making sure the entry is not duplicated
            $query = BaseCalendar::where('grade_id', $entry['grade_id'])
            ->where('subject_id', $entry['subject_id'])
            ->where('title', $entry['title'])->get();
            
            if(count($query) > 0){
            res::error("in entry number $entryNum ... this title already exists with this subject and class or this entry is duplicated in the input",code:422, rollback:true);
            }

            try{
            $calendar = BaseCalendar::create($entry);
            }
            catch(Exception $e){
                res::error("something went wrong in entry $entryNum.", $e->getMessage(),rollback:true);
            }
            $entryNum++;

            $result[] = $calendar;
        }

        DB::commit();
        res::success('plans created successfully', $result);
    }

    public function edit(Request $request, $calendar_id){

        $data = $this->validateCalendar($request, $calendar_id);

        $calendar = BaseCalendar::find($calendar_id);

        if(!$calendar){
            res::error('this base calendar id is not valid',code:422);
        }
        if(count($data) != 1){
            res::error('you can edit 1 plan at a time', code:422);
        }

        Helper::lazyQueryTry(fn()=>$calendar->update($data[0]));
    
        res::success('plan updated successfully', $calendar);
    }

    public function validateCalendar($request, $calendar_id = null){

        $data = $request->validate([
            '*' => ['required', 'array'],
            '*.subject_id' => [
                'required',
                'exists:subjects,id',
            ],
            '*.grade_id' => 'required|exists:grades,id',
            '*.title' => 'required|min:1|max:30',
            '*.date' => 'required|date',
            '*.is_test' => 'required|boolean'
        ]);
        
        $entryNum = 1;
        foreach($data as $entry){

            $grade = Grade::find($entry['grade_id']);
            $subject = Subject::find($entry['subject_id']);
            
            //making sure the subject belongs to the grade
            if (!$grade->subjects->contains($subject)) {
                res::error("in entry number $entryNum ... The subject does not belong to the specified grade", code:422);
            }

            //making sure the entry is not duplicated
            $query = BaseCalendar::where('grade_id', $entry['grade_id'])
            ->where('subject_id', $entry['subject_id'])
            ->where('title', $entry['title'])->get();
            
            if((count($query) == 1 && $query[0]->id != $calendar_id) ||
                count($query) > 1){
                res::error("in entry number $entryNum ... this title already exists with this subject and class or this entry is duplicated in the input",code:422);
            }
            $entryNum++;
        }
        return $data;
    }

    //---------------------------------------------------------

    public function getCalendar(){

        request()->validate([
            'subject_ids' => 'array',
            'subject_ids.*' => 'exists:subjects,id',
            'grade_ids' => 'array',
            'grade_ids.*' => 'exists:grades,id',
        ]);

        $query = BaseCalendar::query();

        if(request()->has('subject_ids')){
            $query->whereIn('subject_id', request()->subject_ids);
        }

        if(request()->has('grade_ids')){
            $query->whereIn('grade_id', request()->grade_ids);
        }

        $calendars = $query->orderBy('date', 'asc')->get();
            
            return res::success('calendar retrieved successfully', $calendars);
        }
}
