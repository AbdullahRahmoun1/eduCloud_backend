<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GClassController extends Controller
{
    public function getAllClasses(Grade $grade) {
        return $grade->g_classes()->get();
    }
}
