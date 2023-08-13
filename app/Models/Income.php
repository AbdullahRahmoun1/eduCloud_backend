<?php

namespace App\Models;

use App\Models\Student;
use App\Models\MoneySubRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory;
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
    protected $guarded =['created_at','updated_at'];
    public const DUP_MSG="There is a receipt with the same receipt_number";

    public function student(): BelongsTo {
        return $this->belongsTo(Student::class);
    }

    public function moneySubRequest(): BelongsTo {
        return $this->belongsTo(MoneySubRequest::class);
    }
}

