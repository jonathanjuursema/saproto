<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongCategory extends Model
{
    use HasFactory;

    protected $table = 'codex_category';

    public function songs(): HasMany
    {
        return $this->hasMany(CodexSong::class, 'category_id');
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            $category->songs()->delete();
        });
    }
}
