<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NumberController extends Controller
{
    public function khara()
    {
        
        return request()->user()->can('fuck')?'yes':'no';
    }
}
