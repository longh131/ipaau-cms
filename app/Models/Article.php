<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'content',
        'summary',
        'author',
        'source',
        'view_count',
        'cover_image',
        'redirect_url',
        'published_at',
        'is_featured',
        'is_sticky',
        'is_active',
        'sort_order',
        'extra_fields',
    ];

    protected $casts = [
        'extra_fields' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_sticky' => 'boolean',
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'sort_order' => 0,
        'view_count' => 0,
        'is_sticky' => false,
        'is_featured' => false,
        'is_active' => true,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
