<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UNUSED, when implemented in the new protube we want to preserve this data so for now left unused*/

/**
 * Soundboard Sound Model.
 *
 * @property int $id
 * @property int $file_id
 * @property bool $hidden
 * @property string $name
 * @property-read StorageEntry $file
 *
 * @method static Builder|SoundboardSound whereFileId($value)
 * @method static Builder|SoundboardSound whereHidden($value)
 * @method static Builder|SoundboardSound whereId($value)
 * @method static Builder|SoundboardSound whereName($value)
 * @method static Builder|SoundboardSound newModelQuery()
 * @method static Builder|SoundboardSound newQuery()
 * @method static Builder|SoundboardSound query()
 *
 * @mixin Eloquent
 */
class SoundboardSound extends Model
{
    protected $table = 'soundboard_sounds';

    protected $guarded = ['id'];

    public $timestamps = false;

    /** @return BelongsTo */
    public function file()
    {
        return $this->belongsTo(StorageEntry::class, 'file_id');
    }
}
