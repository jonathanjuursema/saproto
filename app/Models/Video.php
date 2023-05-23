<?php

namespace Proto\Models;

use DateInterval;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Video Model.
 *
 * @property int $id
 * @property string $title
 * @property int|null $event_id
 * @property string $youtube_id
 * @property string $youtube_title
 * @property string $youtube_length
 * @property string $youtube_user_id
 * @property string $youtube_user_name
 * @property string $youtube_thumb_url
 * @property string $video_date
 * @property-read Event|null $event
 *
 * @method static Builder|Video whereEventId($value)
 * @method static Builder|Video whereId($value)
 * @method static Builder|Video whereTitle($value)
 * @method static Builder|Video whereVideoDate($value)
 * @method static Builder|Video whereYoutubeId($value)
 * @method static Builder|Video whereYoutubeLength($value)
 * @method static Builder|Video whereYoutubeThumbUrl($value)
 * @method static Builder|Video whereYoutubeTitle($value)
 * @method static Builder|Video whereYoutubeUserId($value)
 * @method static Builder|Video whereYoutubeUserName($value)
 * @method static Builder|Video newModelQuery()
 * @method static Builder|Video newQuery()
 * @method static Builder|Video query()
 *
 * @mixin Eloquent
 */
class Video extends Model
{
    protected $table = 'videos';

    protected $guarded = ['id'];

    public $timestamps = false;

    /** @return BelongsTo */
    public function event()
    {
        return $this->belongsTo('Proto\Models\Event', 'event_id');
    }

    /** @return string */
    public function getYouTubeUrl()
    {
        return sprintf('https://www.youtube.com/watch?v=%s', $this->youtube_id);
    }

    /** @return string */
    public function getYouTubeChannelUrl()
    {
        return sprintf('https://www.youtube.com/channel/%s', $this->youtube_user_id);
    }

    /** @return string */
    public function getYouTubeEmbedUrl()
    {
        return sprintf('https://www.youtube.com/embed/%s?rel=0', $this->youtube_id);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getHumanDuration()
    {
        $interval = new DateInterval($this->youtube_length);
        if ($interval->y > 0) {
            return sprintf('%s years', $interval->y);
        } elseif ($interval->m > 0) {
            return sprintf('%s months', $interval->m);
        } elseif ($interval->d > 0) {
            return sprintf('%s days', $interval->d);
        } elseif ($interval->h > 0) {
            return sprintf('%s:%s:%s ', $interval->h, str_pad(strval($interval->i), 2, '0', STR_PAD_LEFT), str_pad(strval($interval->s), 2, '0', STR_PAD_LEFT));
        } elseif ($interval->i > 0) {
            return sprintf('%s:%s ', $interval->i, str_pad(strval($interval->s), 2, '0', STR_PAD_LEFT));
        } else {
            return sprintf('%s seconds', $interval->s);
        }
    }

    /** @return string|false */
    public function getUnixTimeStamp()
    {
        return date('U', strtotime($this->video_date));
    }

    /** @return string|false */
    public function getFormDate()
    {
        return date('d-m-Y', strtotime($this->video_date));
    }
}
