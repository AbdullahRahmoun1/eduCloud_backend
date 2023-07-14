<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Employee;
use Exception;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    
    public function add(){
        $f=['required','string','between:5,25'];
        $l=$f;
        $l[]=Rule::unique('employees','last_name')
        ->where('first_name',request('first_name'));
        $data=request()->validate([
            'first_name'=>$f,
            'last_name'=>$l,
            'roles'=>['required','array','between:1,4'],
        ],[
            'last_name.unique'=>'This employee is already in the system'
        ]);
        //check if roles are valid
        foreach($data['roles'] as $role)
            if(config('roles.'.$role,-1)==-1)
                abort(422,"Role $role isn't an actual role..");
        //create the employee
        $emp=Employee::create($data);
        //assign the roles to him
        array_map(fn($role)=>$emp->assignRole($role)
        ,$data['roles']);
        //now create an account for him :)  
        $acc=Account::createAccount($emp, 1);
        //.................
        return [
            'message'=>'Employee was added successfully',
            'account info'=>$acc
        ];

    }

}
