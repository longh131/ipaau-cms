<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}