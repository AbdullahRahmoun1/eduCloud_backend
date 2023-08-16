<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Grade;
use Illuminate\Validation\Rule;
use App\Helpers\ResponseFormatter as res;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function addSubjectsToGrade(Grade $grade) {
        $unique=Rule::unique('subjects','name')
        ->where('grade_id',$grade->id);
        $data=request()->validate([
            '*.name'=>['required','string','between:1,45',$unique],
            '*.min_mark'=>['required','numeric','min:1'],            
            '*.max_mark'=>['required','numeric','min:1'],            
            '*.notes'=>['string','between:1,100'],            
        ],[
            '*.name.unique'=>"The subject ( :input ) is already associated with the grade ( {$grade->name} )'"
        ]);
        if(empty($data)){
            return res::error('WTH?..Empty body',code:422);
        }
        DB::beginTransaction();
        $count=0;
            foreach($data as $subject){
                $subject['grade_id']=$grade->id;
                Subject::create($subject);
                $count++;
            }
        try {
            
        } catch (QueryException $e) {
            $dupMsg = "Subject {$data[$count]['name']} exists twice in your input..Try removing one of them";
            res::queryError($e,$dupMsg,true);
        }
        DB::commit();
        res::success();
    }
    public function  edit(Subject $subject) {
        $unique=Rule::unique('subjects','name')
        ->where('grade_id',$subject->grade->id)
        ->whereNotIn('id',[$subject->id]);
        $data=request()->validate([
            'name'=>['string','between:1,45',$unique],
            'min_mark'=>['numeric','min:1'],            
            'max_mark'=>['numeric','min:1'],            
            'notes'=>['string','between:1,100'],            
        ],[
            'name.unique'=>'Failed.. subject ( :input ) already exists!'
        ]);
        if(isset($data['name']))
        $subject->name=$data['name'];
        if(isset($data['min_mark']))
        $subject->min_mark=$data['min_mark'];
        if(isset($data['max_mark']))
        $subject->max_mark=$data['max_mark'];
        if(isset($data['notes']))
        $subject->notes=$data['notes'];
        Helper::lazyQueryTry(fn()=>$subject->save());
        res::success();
    }
    public function view(Subject $subject){
        $subject->load([
            'g_classes',
            'grade',
            'g_classes.teachers'=> fn($query)=>$query->where('subject_id',$subject->id)
        ]
        );
        res::success(data:$subject);
    }
}
