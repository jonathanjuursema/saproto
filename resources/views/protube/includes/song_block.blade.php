<div class="card mb-3 leftborder leftborder-info">
    <div class="card-body">
        <p class="card-text ellipsis">
            <strong>
                <i class="fas fa-music fa-fw me-2"></i>
                @if (! empty($video->spotify_id))
                    {{ $video->spotify_name }}
                @else
                    {{ $video->video_title }}
                @endif
            </strong>

            @if (! isset($hide_played))
                <br />

                <span class="text-muted">
                    <em>
                        Played
                        {{ isset($played_count) ? $played_count : $video->played_count }}
                        times.
                    </em>
                </span>
            @endif

            @if (isset($show_text))
                <br />

                <span class="text-muted">
                    <em>{{ $show_text }}</em>
                </span>
            @endif
        </p>

        <div class="row">
            <div class="col-6">
                <a
                    href="{{ App\Models\PlayedVideo::generateYoutubeUrl($video->video_id) }}"
                    target="_blank"
                    class="btn btn-xs btn-outline-danger btn-block btn-sm"
                >
                    <i class="fab fa-youtube fa-fw me-2" aria-hidden="true"></i>
                    Watch on YouTube
                </a>
            </div>

            <div class="col-6">
                @if (! empty($video->spotify_id))
                    <a
                        href="{{ App\Models\PlayedVideo::generateSpotifyUri($video->spotify_id) }}"
                        target="_blank"
                        class="btn btn-xs btn-outline-primary btn-block btn-sm"
                    >
                        <i
                            class="fab fa-spotify fa-fw me-2"
                            aria-hidden="true"
                        ></i>
                        Listen on Spotify
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
