<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as res; 
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function getAllGrades() {
        res::success(data:Grade::all());
    }

    public function add(){
        $data=request()->validate([
            'name'=>['required','unique:grades,name'],
        ],[
            'name.unique'=>'This grade is already in the system'
        ]);
        Helper::lazyQueryTry(fn()=>Grade::create($data));
        res::success();
    }
    public function edit(Grade $grade){
        $data=request()->validate([
            'name'=>['required','unique:grades,name'],
        ],[
            'name.unique'=>'Failed. The grade you entered is already in the system!'
        ]);
        $grade->name=$data['name'];
        Helper::lazyQueryTry(fn()=>$grade->save());
        res::success();
    }
    public function view(Grade $grade){
        $grade->load([
            'g_classes:id,name,grade_id',
            'subjects:id,name,grade_id'
        ]);
        res::success(data:$grade);
    }
    public function getAllGradesWithClassesAndSubjects(Grade $grade) {
        $result = $grade->with('g_classes', 'subjects')->get();
        res::success(data:$result);
    }
}
