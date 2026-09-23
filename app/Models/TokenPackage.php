<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tokens',
        'price_usd',
        'badge',
        'is_popular',
        'is_active',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'tokens' => 'integer',
        'price_usd' => 'float',
    ];
}
