<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function ($menu) {
            if (empty($menu->location)) {
                $menu->location = Str::slug($menu->name);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }
}