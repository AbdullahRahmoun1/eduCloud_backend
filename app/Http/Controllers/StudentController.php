<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Helpers\ResponseFormatter;
use App\Models\Account;
use App\Models\CandidateStudent;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{

    public function add($is_direct, Request $r) {

        //initialize the rules for validation 
        $namesV = ['required', 'string', 'between:3,20']; 
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

        $studentRules['g_class_id'] = ['required', 'exists:g_classes,id']; 
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
            return ResponseFormatter::error('something went wrong', $e->getMessage());
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
            return ResponseFormatter::error('this student id is invalid.', null, 422);

        $first_n = $r['first_name'] ? $r['first_name'] : $student['first_name'];
        $last_n = $r['last_name'] ? $r['last_name'] : $student['last_name'];
        $father_n = $r['father_name'] ? $r['father_name'] : $student['father_name'];
        $mother_n = $r['mother_name'] ? $r['mother_name'] : $student['mother_name'];

        $namesV = ['string', 'between:3,20', 'nullable']; 
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
        return ResponseFormatter::success('student info was updated successfully.', $data);
        

    }

    public function regeneratePassword(Student $student){

        $account = $student->account;
        if(!isset($account)){
            return ResponseFormatter::error('this student does not have an account',null,422);
        }

        try{
            $new_password = Account::changePassword($account);
        }
        catch(Exception $e){
            return ResponseFormatter::error('something went wrong', $e->getMessage());
        }

        return ResponseFormatter::success('password changed successfully.',[
            'account' => $account['user_name'],
            'new password' => $new_password
        ]);
    }
}
