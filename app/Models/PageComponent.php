<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_slug',
        'component_type',
        'sort_order',
        'data',
        'is_active',
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
    ];
}