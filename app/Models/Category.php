<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getTypeOptions(): array
    {
        return [
            'article' => '文章',
            'page' => '单页',
            'link' => '链接',
            'product' => '产品/服务',
            'case' => '案例',
            'gallery' => '图片画廊',
            'event' => '活动',
            'member' => '会员',
            'download' => '下载',
            'faq' => '常见问题',
            'form' => '表单',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function page(): HasOne
    {
        return $this->hasOne(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public static function getSortedTree()
    {
        return self::where('parent_id', 0)
            ->orderBy('sort_order')
            ->with('allChildren')
            ->get();
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * @param  callable(Category): bool  $include
     * @return array<int, string>
     */
    public static function flatTreeSelectOptions(callable $include, string $childPrefix = '→ '): array
    {
        $options = [];

        static::appendTreeSelectOptions(static::getSortedTree(), $options, $include, '', $childPrefix);

        return $options;
    }

    /**
     * @param  iterable<int, Category>  $categories
     * @param  array<int, string>  $options
     * @param  callable(Category): bool  $include
     */
    protected static function appendTreeSelectOptions(
        iterable $categories,
        array &$options,
        callable $include,
        string $prefix,
        string $childPrefix,
    ): void {
        foreach ($categories as $category) {
            if ($include($category)) {
                $options[$category->id] = $prefix.$category->name;
            }

            $children = $category->relationLoaded('allChildren')
                ? $category->allChildren
                : $category->children;

            if ($children && $children->count() > 0) {
                static::appendTreeSelectOptions($children, $options, $include, $prefix.$childPrefix, $childPrefix);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    public static function pageSelectOptions(?int $pageId = null): array
    {
        $boundCategoryIds = Page::query()
            ->when($pageId, fn ($query) => $query->whereKeyNot($pageId))
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->all();

        return static::flatTreeSelectOptions(function (Category $category) use ($boundCategoryIds): bool {
            if ($category->type !== 'page' || ! $category->is_active) {
                return false;
            }

            return ! in_array($category->id, $boundCategoryIds, true);
        });
    }
}