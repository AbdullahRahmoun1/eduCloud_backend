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
            return response::error('WTH?..Empty body',code:422);
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
            return response::error('WTH?..Empty body',code:422);
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
    
}
