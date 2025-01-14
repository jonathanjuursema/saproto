@include(
    'website.home.cards.card-bg-image',
    [
        'url' => route('video::show', ['id' => $video->id]),
        'img' => $video->youtube_thumb_url,
        'html' => sprintf(
            '<em>%s</em><br><strong><i class="fas fa-fw fa-play" aria-hidden="true"></i> %s</strong>',
            date('M j, Y', strtotime($video->video_date)),
            $video->title,
        ),
        'photo_pop' => isset($photo_pop) ? $photo_pop : true,
    ]
)
