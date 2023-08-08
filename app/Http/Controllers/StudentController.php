<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter as response;
use App\Models\Account;
use App\Models\CandidateStudent;
use App\Models\GClass;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{

    public function add($is_direct, Request $r) {

        //initialize the rules for validation 
        $namesV = ['required', 'string', 'between:2,20']; 
        $uniqueStu = $namesV; 
        $uniqueStu[] = Rule::unique('students', 'mother_name')->where('first_name',$r['first_name'])->where('last_name', $r['last_name'])->where('father_name', $r['father_name']); 
        $uniqueCand = $namesV; 
        $uniqueCand[] = Rule::unique('candidate_students', 'mother_name')->where('first_name',$r['first_name'])->where('last_name', $r['last_name'])->where('father_name', $r['father_name']); 
        $namesV30 = ['string', 'max:30', 'nullable']; 
        
        $candidateRules = [ 
            'first_name' => $namesV, 
            'last_name' => $namesV, 
            'father_name' => $namesV, 
            'place_of_living' => ['string', 'min:3', 'max:45', 'nullable'], 
            'birth_date' => ['required', 'date'], 
            '6th_grade_avg' => ['numeric', 'nullable', 'gt:0'] 
        ]; 
        
        $studentRules = array_merge($candidateRules, [ 
            'birth_place' => ['string', 'min:3', 'max:45', 'nullable'], 
            'social_description' => ['string' , 'min:3', 'max:65', 'nullable'], 
            'grand_father_name' => $namesV30, 
            'mother_last_name' => $namesV30, 
            'public_record' => ['string', 'max:30', 'nullable'], 
            'father_alive' => ['boolean', 'nullable'], 
            'mother_alive' => ['boolean', 'nullable'], 
            'father_profession' => $namesV30, 
            'previous_school' => $namesV30, 
            'address_id' => ['exists:addresses,id', 'nullable'], 
            'transportation_subscriber' => ['boolean', 'nullable'], 
            'registration_place' => ['string', 'min:3', 'max:40', 'nullable'], 
            'registration_number' => ['string', 'min:1', 'max:30', 'nullable'], 
            'registration_date' => ['date', 'nullable'], 
            'notes' => ['string', 'min:1', 'max:200', 'nullable'], 
        ]); 
        $candidateRules['grade_id'] = ['required', 'exists:grades,id']; 
        $candidateRules['mother_name'] = $uniqueCand; 

        $studentRules['g_class_id'] = ['exists:g_classes,id', 'nullable']; 
        $studentRules['mother_name'] = $uniqueStu; 
        
        //validate 
        $data = request()->validate(
            $is_direct ? $studentRules : $candidateRules, 
            ['mother_name.unique' => 'this student is already in the system']);

        DB::beginTransaction();
        try{
            //model creation
            $student = $is_direct ? Student::create($data) :
                CandidateStudent::create($data);

            //account creation
            if($is_direct){
                $student->assignRole(config('roles.student'));
                $acc = Account::createAccount($student, 0);
            }
        }
        catch(Exception $e){
            DB::rollBack();
            return response::error('something went wrong', $e->getMessage());
        }
        DB::commit();
        //.............
        return $is_direct ? [
            'message' => 'Student was added successfully.',
            'data' => $acc] :
            ['message' => 'Was successfully added as a Candidate student.'];
        
    }

    public function edit($id,$is_candidate, Request $r) {

        //initialize the rules for validation 
        $student = $is_candidate ? CandidateStudent::find($id) : Student::find($id);
        if(!isset($student))
            return response::error('this student id is invalid.', null, 422);

        $first_n = $r['first_name'] ? $r['first_name'] : $student['first_name'];
        $last_n = $r['last_name'] ? $r['last_name'] : $student['last_name'];
        $father_n = $r['father_name'] ? $r['father_name'] : $student['father_name'];
        $mother_n = $r['mother_name'] ? $r['mother_name'] : $student['mother_name'];

        $namesV = ['string', 'between:2,20', 'nullable']; 
        $uniqueStu = $namesV; 
        $uniqueStu[] = Rule::unique('students', 'mother_name')->where('first_name',$first_n)->where('last_name', $last_n)->where('father_name', $father_n); 
        $uniqueCand = $namesV; 
        $uniqueCand[] = Rule::unique('candidate_students', 'mother_name')->where('first_name',$r['first_name'])->where('last_name', $r['last_name'])->where('father_name', $r['father_name']); 
        $namesV30 = ['string', 'max:30', 'nullable']; 
        
        $candidateRules = [ 
            'first_name' => $namesV, 
            'last_name' => $namesV, 
            'father_name' => $namesV, 
            'place_of_living' => ['string', 'min:3', 'max:45', 'nullable'], 
            'birth_date' => ['date', 'nullable'], 
            '6th_grade_avg' => ['numeric', 'nullable', 'gt:0'] 
        ]; 
        
        $studentRules = array_merge($candidateRules, [ 
            'birth_place' => ['string', 'min:3', 'max:45', 'nullable'], 
            'social_description' => ['string' , 'min:3', 'max:65', 'nullable'], 
            'grand_father_name' => $namesV30, 
            'mother_last_name' => $namesV30, 
            'public_record' => ['string', 'max:30', 'nullable'], 
            'father_alive' => ['boolean', 'nullable'], 
            'mother_alive' => ['boolean', 'nullable'], 
            'father_profession' => $namesV30, 
            'previous_school' => $namesV30, 
            'address_id' => ['exists:addresses,id', 'nullable'], 
            'transportation_subscriber' => ['boolean', 'nullabl'], 
            'registration_place' => ['string', 'min:3', 'max:40', 'nullable'], 
            'registration_number' => ['string', 'min:1', 'max:30', 'nullable'], 
            'registration_date' => ['date', 'nullable'], 
            'notes' => ['string', 'min:1', 'max:200', 'nullable'], 
        ]); 
        $candidateRules['grade_id'] = ['exists:grades,id', 'nullable']; 
        $candidateRules['mother_name'] = $uniqueCand; 

        $studentRules['g_class_id'] = ['exists:g_classes,id', 'nullable']; 
        $studentRules['mother_name'] = $uniqueStu; 
        
        //validate 
        $data = request()->validate(
            $is_candidate ? $candidateRules : $studentRules, 
            ['mother_name.unique' => 'this student is already in the system']); 
        
        //update the student
        Helper::lazyQueryTry(fn()=> $student->update($data));
        return response::success('student info was updated successfully.', $data);
        

    }

    public function regeneratePassword(Student $student){

        $account = $student->account;
        if(!isset($account)){
            return response::error('this student does not have an account',null,422);
        }

        try{
            $new_password = Account::changePassword($account);
        }
        catch(Exception $e){
            return response::error('something went wrong', $e->getMessage());
        }

        return response::success('password changed successfully.',[
            'account' => $account['user_name'],
            'new password' => $new_password
        ]);
    }

    public function search(){
        $correctClass = Rule::exists('g_classes','id')->where(function($query){
            $query->where('grade_id',request()->grade_id);
        });
        $data = request()->validate([
            'grade_id' => ['required_with:class_id', 'exists:grades,id'],
            'class_id' => [$correctClass]
        ],[
            "class_id.exists" => 'this grade doesn\'t have this class_id.'
        ]);

        $employee = auth()->user()->owner;
        $search=request()->search;
        $searchable_classes = $employee->hasRole(config('roles.supervisor')) &&
            !($employee->hasRole('roles.secretary') || $employee->hasRole('roles.principal')) ?
            $employee->g_classes_sup : GClass::all();
        
        $result = Student::query()
        ->where(DB::raw('CONCAT(first_name," ",last_name)'),'like',"%$search%")
        ->whereIn('g_class_id', $searchable_classes->pluck('id'));

        if(isset(request()->grade_id)){
            $result->whereHas('g_class.grade', function($query){
                $query->where('id',request()->grade_id);
            });
        }

        if(isset(request()->class_id)){
            $result->where('g_class_id',request()->class_id);
        }

        $result = $result->orderBy('first_name')->orderBy('last_name');

        if(request()->has('page'))
            $result = $result->simplePaginate(10);
        else
        $result = $result->get();
        response::success('results found successfully', $result);
    }
    
}
