<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as response;
class TypeController extends Controller
{
    public function add() {
        $data = request()->validate([
            'name' => ['required','min:2', 'max:20', 'unique:types,name']
        ]);

        $type = Type::create($data);
        
        response::success('type added successfully', $type);
    }

    public function edit(Type $type){
        $data = request()->validate([
            'name' => ['required','min:2', 'max:20', 'unique:types,name']
        ]);

        $type->update($data);

        response::success('type edited successfully', $type);
    }

    public function getAllTypes(){
        $types = Type::all();

        isset($types[0]) ? response::success(data:$types) :
            response::error('no types found.');
    }

    public function getNameOfType($id){
        $type = Type::find($id);
        if(!$type)
        response::error('this type id is not valid');
        response::success(data:$type);
    }
}
