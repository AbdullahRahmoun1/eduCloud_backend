<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Student;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function test(Request $request)
    {   
        $val = Validator::make($request->all(), [
            'n' => [['required', 'max:7'], 'min:5'],
            'm' => 'integer|nullable'
        ]);
        
        return ResponseFormatter::error(null, 'shit' , 512);
    }

}
