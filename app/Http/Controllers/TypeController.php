<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
class TypeController extends Controller
{
    public function add() {
        $data = request()->validate([
            'name' => ['required','min:2', 'max:20', 'unique:types,name']
        ]);

        $type = Type::create($data);
        
        res::success('type added successfully', $type);
    }

    public function edit(Type $type){
        $data = request()->validate([
            'name' => ['required','min:2', 'max:20', 'unique:types,name']
        ]);

        $type_id = $type->id;
        if(in_array($type_id, [1,2,3])){
            res::error('you can not edit this type, it is in the system in default');
        }

        $type->update($data);

        res::success('type edited successfully', $type);
    }

    public function getAllTypes(){
        $types = Type::all();

        isset($types[0]) ? res::success(data:$types) :
            res::error('no types found.');
    }

    public function getNameOfType($id){
        $type = Type::find($id);
        if(!$type)
            res::error('this type id is not valid');
        res::success(data:$type);
    }
}
