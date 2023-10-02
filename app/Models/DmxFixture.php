<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * DmxFixture Model.
 *
 * @property int $id
 * @property string $name
 * @property int $channel_start
 * @property int $channel_end
 * @property int $follow_timetable
 *
 * @method static Builder|DmxFixture whereChannelEnd($value)
 * @method static Builder|DmxFixture whereChannelStart($value)
 * @method static Builder|DmxFixture whereFollowTimetable($value)
 * @method static Builder|DmxFixture whereId($value)
 * @method static Builder|DmxFixture whereName($value)
 * @method static Builder|DmxFixture newModelQuery()
 * @method static Builder|DmxFixture newQuery()
 * @method static Builder|DmxFixture query()
 *
 * @mixin Eloquent
 */
class DmxFixture extends Model
{
    protected $table = 'dmx_fixtures';

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * @param  string|null  $special_func
     * @return Collection|DmxChannel[]
     */
    public function getChannels($special_func = null)
    {
        $channels = DmxChannel::where('id', '>=', $this->channel_start)
            ->where('id', '<=', $this->channel_end);

        if ($special_func) {
            $channels = $channels->where('special_function', $special_func);
        }

        return $channels->orderBy('id', 'asc')->get();
    }

    /** @return int[] */
    public function getChannelNumbers()
    {
        return range($this->channel_start, $this->channel_end);
    }
}
