<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * DmxChannel Model.
 *
 * @property int $id
 * @property string $name
 * @property string $special_function
 *
 * @method static Builder|DmxChannel whereId($value)
 * @method static Builder|DmxChannel whereName($value)
 * @method static Builder|DmxChannel whereSpecialFunction($value)
 * @method static Builder|DmxChannel newModelQuery()
 * @method static Builder|DmxChannel newQuery()
 * @method static Builder|DmxChannel query()
 *
 * @mixin Eloquent
 */
class DmxChannel extends Model
{
    protected $table = 'dmx_channels';

    protected $fillable = ['id', 'name'];

    public $timestamps = false;
}
