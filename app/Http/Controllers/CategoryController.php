<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter as res;
class CategoryController extends Controller
{
    public function test()
    {
        $this->authorize('create');
        return 'good to go';
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:30|unique:categories',
            'send_directly' => ['required', 'boolean']
        ],['name.unique' => 'this category already exists']);

        $category = Category::create($data);

        res::success('Category created successfully', $category);
    }

    public function edit(Request $request, $category_id){

        $category = Category::find($category_id);
        if(!$category){
            res::error('this category id is not valid',code:422);
        }

        $data = $request->validate([
            'name' => 'required|string|max:30|unique:categories',
            'send_directly' => ['boolean']
        ],['name.unique' => 'this category already exists']);

        $category->update($data);

        res::success(data:$category);
    }

    public function getAll(){
        res::success(data:Category::all());
    }
}
