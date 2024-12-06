<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Codex song category model.
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CodexSongCategory extends Model
{
    use HasFactory;

    protected $table = 'codex_category';

    public function songs(): HasMany
    {
        return $this->hasMany(CodexSong::class, 'category_id');
    }

    protected static function booted()
    {
        static::deleting(static function ($category) {
            $category->songs()->delete();
        });
    }
}
