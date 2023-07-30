<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class CandidateStudentController extends Controller
{
    public function all(Grade $grade){
        $candidates=$grade->candidates;
        $candidates->load(['atMarks']);
        return $candidates;
    }
}
