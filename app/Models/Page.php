<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    public const TEMPLATE_DEFAULT = 'default';

    /** @var array<string, string> */
    public const TEMPLATE_OPTIONS = [
        self::TEMPLATE_DEFAULT => '默认正文页',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'template',
        'data',
        'content',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'data' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            if (! $page->category_id) {
                return;
            }

            $category = Category::query()->find($page->category_id);

            if ($category) {
                $page->slug = $category->slug;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function displayTitle(): string
    {
        return filled($this->title) ? $this->title : (string) ($this->category?->name ?? '');
    }

    public function seoTitle(): string
    {
        return filled($this->meta_title) ? $this->meta_title : $this->displayTitle();
    }
}
