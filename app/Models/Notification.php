<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $hidden=['created_at','updated_at'];
    protected $guarded =['created_at','updated_at'];
    public function owner()
    {
        return $this->morphTo();
    }   
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
