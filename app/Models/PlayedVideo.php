<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Played Video Model.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $video_id
 * @property string $video_title
 * @property string|null $spotify_id
 * @property string|null $spotify_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder|PlayedVideo whereCreatedAt($value)
 * @method static Builder|PlayedVideo whereId($value)
 * @method static Builder|PlayedVideo whereSpotifyId($value)
 * @method static Builder|PlayedVideo whereSpotifyName($value)
 * @method static Builder|PlayedVideo whereUpdatedAt($value)
 * @method static Builder|PlayedVideo whereUserId($value)
 * @method static Builder|PlayedVideo whereVideoId($value)
 * @method static Builder|PlayedVideo whereVideoTitle($value)
 * @method static Builder|PlayedVideo newModelQuery()
 * @method static Builder|PlayedVideo newQuery()
 * @method static Builder|PlayedVideo query()
 *
 * @mixin Eloquent
 */
class PlayedVideo extends Model
{
    protected $table = 'playedvideos';

    protected $guarded = ['id'];

    /** @return BelongsTo */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateYoutubeThumbnail(string $youtube_id): string
    {
        return "https://img.youtube.com/vi/{$youtube_id}/mqdefault.jpg";
    }

    public static function generateSpotifyUri(string $spotify_id): string
    {
        $spotify_id = str_replace('spotify:track:', '', $spotify_id);

        return "https://open.spotify.com/track/{$spotify_id}";
    }

    public static function generateYoutubeUrl(string $youtube_id): string
    {
        return "https://www.youtube.com/watch?v={$youtube_id}";
    }
}
