<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'file_path',
        'image',
        'sort_order',
        'is_active',
        'extra_fields',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra_fields' => 'array',
    ];
}