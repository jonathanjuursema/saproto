<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Display Model.
 *
 * @property int $id
 * @property string $url
 * @property string $name
 * @property string $display
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Display whereCreatedAt($value)
 * @method static Builder|Display whereDisplay($value)
 * @method static Builder|Display whereId($value)
 * @method static Builder|Display whereName($value)
 * @method static Builder|Display whereUpdatedAt($value)
 * @method static Builder|Display whereUrl($value)
 * @method static Builder|Display newModelQuery()
 * @method static Builder|Display newQuery()
 * @method static Builder|Display query()
 *
 * @mixin Eloquent
 */
class Display extends Model
{
    protected $table = 'displays';

    protected $guarded = ['id'];
}
