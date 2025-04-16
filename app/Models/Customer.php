<?php

namespace App\Models;

use App\Enums\DayEnum;
use App\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'owner',
        'phone',
        'is_active',
        'is_phone',
       'work_type',
        'payment_day',
        'code',
    ];

    protected $casts = [
        'work_type'=> TypeEnum::class,
        'payment_day' => DayEnum::class
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');

        // by: HakanKorkz Kral
    }
}
