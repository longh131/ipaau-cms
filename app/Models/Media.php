<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'file_path',
        'thumbnail_path',
        'type',
        'file_size',
        'mime_type',
        'alt_text',
        'description',
        'uploaded_by',
    ];

    protected static function booted()
    {
        static::saving(function ($media) {
            if (!$media->slug && $media->name) {
                $media->slug = \Illuminate\Support\Str::slug($media->name);
            }
        });
    }

    public function getFormattedSizeAttribute()
    {
        if (!$this->file_size) {
            return '0 B';
        }
        $bytes = $this->file_size;
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
