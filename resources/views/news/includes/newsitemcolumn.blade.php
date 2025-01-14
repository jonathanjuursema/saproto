<div class="card mb-3">
    <div class="card-header">
        <i class="fas fa-newspaper"></i>
        {{ $text }}
    </div>

    <div class="card-body">
        <div class="row">
            @if (count($items) == 0)
                <div class="text-center">
                    <br />
                    <strong>There are currently no {{ $text }}.</strong>
                </div>
            @else
                @foreach ($items as $index => $newsitem)
                    <div class="col-md-4 col-sm-6">
                        @php
                            $class = $newsitem->is_weekly ? 'bg-primary' : 'bg-warning';
                            $name = $newsitem->is_weekly ? 'Weekly' : 'News';
                            $weekly = "<span class='badge rounded-pill $class float-end'>$name</span>";
                            $title = "<strong> $newsitem->title </strong>";
                            $published = '<br>Published ' . Carbon::parse($newsitem->published_at)->diffForHumans() . ' ';
                        @endphp

                        @include(
                            'website.home.cards.card-bg-image',
                            [
                                'url' => $newsitem->url,
                                'img' => $newsitem->featuredImage
                                    ? $newsitem->featuredImage->generateImagePath(500, 300)
                                    : ($newsitem->is_weekly
                                        ? url('images/weekly-cover.png')
                                        : null),
                                'html' => sprintf(
                                    '<div class="w-100">%s %s %s</div>',
                                    $title,
                                    $weekly,
                                    $published,
                                ),
                                'height' => '180',
                                'photo_pop' => $newsitem->featuredImage,
                            ]
                        )
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
