<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CandidateStudent;
use App\Models\Student;
use Illuminate\Http\Request;
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
        $namesV30 = ['string', 'max:30']; 
        
        $candidateRules = [ 
            'first_name' => $namesV, 
            'last_name' => $namesV, 
            'father_name' => $namesV, 
            'place_of_living' => ['string', 'max:45'], 
            'birth_date' => ['required', 'date'], 
            '6th_grade_avg' => 'numeric', 
        ]; 
        
        $studentRules = array_merge($candidateRules, [ 
            'birth_place' => ['string','max:45'], 
            'social_description' => ['string', 'max:65'], 
            'grand_father_name' => $namesV30, 
            'mother_last_name' => $namesV30, 
            'public_record' => ['string', 'max:30'], 
            'father_alive' => 'boolean', 
            'mother_alive' => 'boolean', 
            'father_profession' => $namesV30, 
            'previous_school' => $namesV30, 
            'address_id' => ['exists:addresses,id'], 
            'transportation_subscriber' => 'boolean', 
            'registration_place' => ['string', 'max:40'], 
            'registration_number' => ['string', 'max:30'], 
            'registration_date' => 'date', 
            'notes' => ['string', 'max:200'], 
        ]); 
        $candidateRules['grade_id'] = ['required', 'exists:grades,id']; 
        $candidateRules['mother_name'] = $uniqueCand; 

        $studentRules['g_class_id'] = ['required', 'exists:g_classes,id']; 
        $studentRules['mother_name'] = $uniqueStu; 
        
        //validate 
        $data = request()->validate(
            $is_direct ? $studentRules : $candidateRules, 
            ['mother_name.unique' => 'this student is already in the system']);

        //model creation
        $student = $is_direct ? Student::create($data) :
            CandidateStudent::create($data);

        //account creation
        if($is_direct){
            $student->assignRole(config('roles.student'));
            $acc = Account::createAccount($student, 0);
        }
        //.............
        return $is_direct ? [
            'message' => 'Student was added successfully.',
            'account info' => $acc] :
            ['message' => 'Was successfully added as a Candidate student.'];
    }
}
