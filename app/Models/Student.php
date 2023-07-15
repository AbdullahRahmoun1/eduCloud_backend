<?php

namespace App\Models;

use App\Models\Mark;
use App\Models\GClass;
use App\Models\Number;
use App\Models\Absence;
use App\Models\Account;
use App\Models\MoneyRequest;
use App\Models\Notification;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Student extends Model
{
    use HasFactory,HasRoles;
    protected $hidden=['created_at','updated_at'];
    protected $guarded=[];
    protected $guard_name='web';
    /**
     * Get all of the marks for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get the g_class that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function g_class(): BelongsTo
    {
        return $this->belongsTo(GClass::class);
    }
    
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class,'owner');
    }

    public function account(): MorphOne
    {
        return $this->morphOne(Account::class,'owner');
    }
    public function user(): MorphOne
    {
        return $this->morphOne(Account::class,'owner');
    }

    public function numbers(): MorphMany
    {
        return $this->morphMany(Number::class,'owner');
    }
    
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function moneyRequests(): HasMany{
        return $this->hasMany(MoneyRequest::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }
}