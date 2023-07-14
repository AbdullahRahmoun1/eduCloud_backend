<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function getAllGrades() {
        return Grade::all();
    }
    
    public function getAllClassesAndSubjects(Grade $grade) {
        return $grade->with('g_classes', 'subjects')->get();
    }
}
