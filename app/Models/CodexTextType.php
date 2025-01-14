<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

/**
 * Codex text type model.
 *
 * @property int $id
 * @property string $type
 * @property CodexText[] $texts
 */
class CodexTextType extends Model
{
    use HasFactory;

    protected $table = 'codex_text_types';

    public function texts(): HasMany
    {
        return $this->hasMany(CodexText::class, 'type_id');
    }

    #[Override]
    protected static function booted()
    {
        static::deleting(static function ($type) {
            $type->texts()->delete();
        });
    }
}
