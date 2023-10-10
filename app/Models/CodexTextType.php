<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodexTextType extends Model
{
    use HasFactory;

    protected $table = 'codex_text_types';

    public function texts(): HasMany
    {
        return $this->hasMany(CodexText::class, 'type_id');
    }

    protected static function booted()
    {
        static::deleting(function ($type) {
            $type->texts()->delete();
        });
    }
}
