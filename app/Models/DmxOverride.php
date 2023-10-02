<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * DmxOverride Model.
 *
 * @property int $id
 * @property string $fixtures
 * @property string $color
 * @property string $start
 * @property string $end
 * @property-read bool $is_active
 * @property-read string $window_size
 *
 * @method static Builder|DmxOverride whereColor($value)
 * @method static Builder|DmxOverride whereEnd($value)
 * @method static Builder|DmxOverride whereFixtures($value)
 * @method static Builder|DmxOverride whereId($value)
 * @method static Builder|DmxOverride whereStart($value)
 * @method static Builder|DmxOverride newModelQuery()
 * @method static Builder|DmxOverride newQuery()
 * @method static Builder|DmxOverride query()
 *
 * @mixin Eloquent
 */
class DmxOverride extends Model
{
    protected $table = 'dmx_overrides';

    protected $guarded = ['id'];

    public $timestamps = false;

    /** @return Collection|DmxOverride[] */
    public static function getActiveSorted()
    {
        return self::where('start', '<', date('U'))->where('end', '>', date('U'))->get()->sortBy('window_size');
    }

    /** @return Collection|DmxOverride[] */
    public static function getUpcomingSorted()
    {
        return self::where('start', '>', date('U'))->get()->sortByDesc('start');
    }

    /** @return Collection|DmxOverride[] */
    public static function getPastSorted()
    {
        return self::where('end', '<', date('U'))->get()->sortByDesc('start');
    }

    /** @return array<int, int> */
    public function colorArray()
    {
        return array_map('intval', explode(',', $this->color));
    }

    /** @return int */
    public function red()
    {
        return $this->colorArray()[0];
    }

    /** @return int */
    public function green()
    {
        return $this->colorArray()[1];
    }

    /** @return int */
    public function blue()
    {
        return $this->colorArray()[2];
    }

    /** @return int */
    public function brightness()
    {
        return $this->colorArray()[3];
    }

    /** @return bool */
    public function active()
    {
        return $this->start < date('U') && date('U') < $this->end;
    }

    /** @return bool */
    public function justOver()
    {
        return date('U') > $this->end && date('U') < $this->end. 600;
    }

    /** @return false|string[] */
    public function getFixtureIds()
    {
        return explode(',', $this->fixtures);
    }

    /** @return Collection */
    public function getFixtures()
    {
        return DmxFixture::whereIn('id', $this->getFixtureIds())->get();
    }

    /** @return bool */
    public function getIsActiveAttribute()
    {
        return $this->active();
    }

    /** @return int */
    public function getWindowSizeAttribute()
    {
        return (int) $this->end - (int) $this->start;
    }
}
