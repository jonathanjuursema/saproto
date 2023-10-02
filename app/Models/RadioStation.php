<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Radio Station Model.
 *
 * @property int $id
 * @property string $url
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|RadioStation whereCreatedAt($value)
 * @method static Builder|RadioStation whereId($value)
 * @method static Builder|RadioStation whereName($value)
 * @method static Builder|RadioStation whereUpdatedAt($value)
 * @method static Builder|RadioStation whereUrl($value)
 * @method static Builder|RadioStation newModelQuery()
 * @method static Builder|RadioStation newQuery()
 * @method static Builder|RadioStation query()
 *
 * @mixin Eloquent
 */
class RadioStation extends Model
{
    protected $table = 'radiostations';

    protected $guarded = ['id'];
}
