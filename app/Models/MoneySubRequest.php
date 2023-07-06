<?php

namespace App\Models;

use App\Models\Income;
use App\Models\MoneyRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoneySubRequest extends Model
{
    use HasFactory;
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    protected $guarded =['created_at','updated_at'];
    public function moneyRequest(): BelongsTo {
        return $this->belongsTo(MoneyRequest::class);
    }
    public function incomes(): HasMany {
        return $this->hasMany(Income::class);
    }
}
