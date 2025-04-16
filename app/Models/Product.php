<?php

namespace App\Models;

use App\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'brand_id',
        'type',
        'code'
    ];

    protected function casts(): array
    {
        return [
            'type'=> TypeEnum::class
        ];
    }
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
