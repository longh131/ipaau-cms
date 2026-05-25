<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'sort_order',
        'is_active',
        'extra_fields',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra_fields' => 'array',
    ];
}