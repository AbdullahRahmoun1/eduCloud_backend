<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class MoneyRequestController extends Controller
{
    public function add(Student $student) {
        $data=request()->validate([
            'totalValue'=>['required','min:1000','numeric'],
            'payments'=>['required','array','min:1'],
            'payments.*.value'=>['required','numeric','between:1000,'.request()->totalValue],
            'payments.*.deadline_date'=>['']
        ]);
    }
}
