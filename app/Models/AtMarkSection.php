<?php

namespace App\Models;

use App\Models\AtMark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtMarkSection extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    public function atMark()
    {
        return $this->belongsTo(AtMark::class);
    }
    public function atSection()
    {
        return $this->belongsTo(AtSection::class);
    }
}
