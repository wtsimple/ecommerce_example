<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Tags\HasTags;

class Product extends Model
{
    use HasFactory;
    use HasTags;

    protected $primaryKey = 'sku';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sku', 'name', 'price', 'amount', 'description', 'additional_info', 'avg_rating', 'category'
    ];
}
