<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'form_fields',
        'sort_order',
        'is_active',
        'extra_fields',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'form_fields' => 'array',
        'extra_fields' => 'array',
    ];
}