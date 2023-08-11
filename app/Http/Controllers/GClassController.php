<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Grade;
use App\Models\GClass;
use App\Helpers\Helper;
use PHPUnit\TextUI\Help;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Helpers\ResponseFormatter as response;
use App\Helpers\StudentsDistributer;
use App\Models\Student;

class GClassController extends Controller
{
    public function addClassesToGrade(Grade $grade) {
        $unique=Rule::unique('g_classes','name')
        ->where('grade_id',$grade->id);
        $data=request()->validate([
            '*.name'=>['required',$unique],
            '*.max_number'=>['required','numeric','min:1'],            
        ],[
            '*.name'=>'Class ( :input ) already exists in grade '.$grade
        ]);
        if(empty($data)){
            response::error('WTH?..Empty body',code:422);
        }
        try{
            DB::beginTransaction();
            $result=[];
            $count=0;
            foreach($data as $item) {
                $item['grade_id']=$grade->id;
                $result[]=GClass::create($item);
                $count++;    
            }
        } catch (QueryException $e) {
            $dupMsg="Grade {$grade->name} alrady has a".
            " class named {$data[$count]['name']}";
            response::queryError($e,$dupMsg,rollback:true);
        }    
        DB::commit();
        response::success('Classes created successfully',$result);
    }
    public function edit(GClass $class) {
        $unique=Rule::unique('g_classes','name')
        ->where('grade_id',$class->grade->id)
        ->whereNotIn('id',[$class->id]);
        $data=request()->validate([
            'name'=>[$unique],
            'max_number'=>['numeric','min:1'],            
        ],[
            'name'=>"Class ( :input ) already exists in grade ( {$class->grade->name} )"
        ]);
        if(empty($data)){
            response::error('WTH?..Empty body',code:422);
        }
        if(isset($data['name'])){
            $class->name=$data['name'];
        }
        if(isset($data['max_number'])){
            $class->max_number=$data['max_number'];
        }
        Helper::lazyQueryTry(fn()=>$class->save());
        response::success();
    }
    //is you good
    public function automaticStudentDistribution($algorithm){
        //validate data
        $data=request()->validate([
            'students_ids'=>['required','array','min:1'],
            'students_ids.*'=>['exists:students,id'],
            'classes_ids'=>['required','array','min:1'],
            'classes_ids.*'=>['required','exists:g_classes,id'],
            'force'=>['required','boolean']
        ],[
            'students_ids.*.exists'=>"There is no student with ID :input.",
            'classes_ids.*.exists'=>"There is no class with ID :input.",
        ]);
        //fetch data from db
        $students=Student::whereIn('id',$data['students_ids'])
        ->select([
            'id','first_name','last_name','grade_id','g_class_id'
        ])->get();
        $classes=GClass::whereIn('id',$data['classes_ids'])
        ->get();
        //logical validation
        $sgIds=$students->pluck('grade_id');
        $cgIds=$classes->pluck('grade_id');
        //there is enough spaces for new students
        $freeSpaces=0;
        foreach($classes as $class){
            $freeSpaces+=$class->max_number-$class->students()->count();
        }
        if($freeSpaces<$students->count())
        response::error("There are {$students->count()} new students.. but selected ".
        "classes only have $freeSpaces freeSpaces in total.".
        " Fix? edit the max_capacity of students in one "
        ."or all of the classes then try again.");
        //All students should belong to the same grade
        if(count($sgIds->unique())>1)
        response::error("The students you provided belong to multiple grades!.",code:422);
        //All classes should belong to the same grade
        if(count($cgIds->unique())>1)
        response::error("The classes you provided belong to multiple grades!.",code:422);
        //Students and Grades should belong to the same grade
        if($sgIds[0]!=$cgIds[0])
        response::error("Students and Classes belong to different Grades!.",code:422);
        //Students isn't already in a class
        $already_in_class=$students
        ->filter(fn($s)=>$s->g_class_id!=null);
        if(count($already_in_class) && !$data['force']){
            response::error("Some students already belong to a class.",data:[
                'students_ids'=>$already_in_class->pluck('id')
            ],code:409);
        }
        //All good..Chose the algorithm
        switch($algorithm){
            case "even":
                $result=StudentsDistributer::even_distribution($students,$classes);
                break;
            case "alphabeticPriority":
                $result=StudentsDistributer::priorityDistribution($students,$classes,false);
                break;
            case "reverseAlphabeticPriority":
                $result=StudentsDistributer::priorityDistribution($students,$classes,true);
                break;
            default:    
                response::error("Unknown Algorithm!.",404);
        }
        response::success(data:[
            'classes'=>$result
        ]);
    }

    public function addOrMoveStudentsToClasses(Grade $grade){
        $data=request()->validate([
            'assignments'=>['required','array','min:1'],
            'allowMoving'=>['required','boolean'],
            'assignments.*.class_id'=>['required','exists:g_classes,id'],
            'assignments.*.students_ids'=>['required','array','min:1'],
            'assignments.*.students_ids.*'=>['required','exists:students,id','distinct']
        ]);
        DB::beginTransaction();
        foreach($data['assignments'] as $entry){
            $class=GClass::find($entry['class_id']);
            $students=$entry['students_ids'];
            //class belongs to provided grade
            if($class->grade_id!=$grade->id)
            response::error(
                "Class ( $class->name ) doesn't belong to the grade provided.",
                rollback:true
            );
            //class can add all of these students
            $studentsCount=count($students);
            if($studentsCount>$class->getFreeSpaces())
            response::error(
                "Class ( $class->name ) only has {$class->getFreeSpaces()} "
                ."free spaces. You provided $studentsCount students.",
                rollback:true
            );
            //if moving isn't allowed. is there any student who already has a class
            if(!$data['allowMoving']){
                $alreadyHasClass=Student::whereIn('id',$entry['students_ids'])
                ->whereNotNull('g_class_id');
                if($alreadyHasClass->count()){
                    response::error(
                        "There are {$alreadyHasClass->count()} students that "
                        ."are already in a class. fix: click allow moving students. ",
                        [
                            'students'=>$alreadyHasClass->pluck('full_name')
                        ],
                        rollback:true
                        );
                }
            }
            //All should be good!!
            Student::whereIn('id',$students)
            ->update([
                'g_class_id'=>$class->id
            ]);
        }
        response::success(commit:true);
    }
}
