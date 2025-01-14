@extends('website.home.shared')

@section('greeting')
    <strong>Hi {{ Auth::user()->calling_name }},</strong>
    <br />
    @if ($message != null)
        {!! $message->message !!}
    @else
            Nice to see you back!
    @endif
@endsection

@section('left-column')
    <div class="col-xl-4 col-md-12">
        @include('website.home.cards.featuredevents', ['featuredEvents' => $featuredEvents])

        @include('website.home.cards.leaderboards')

        @if (count($birthdays) > 0)
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-birthday-cake fa-fw me-2"></i>
                    Birthdays
                </div>
                <div class="card-body">
                    @foreach ($birthdays as $key => $user)
                        @php($emojies = ['🎉', '🎈', '🎂', '🎊'])

                        @include(
                            'users.includes.usercard',
                            [
                                'user' => $user,
                                'subtitle' => sprintf(
                                    '<em>has their birthday today!</em> %s',
                                    $emojies[array_rand($emojies)],
                                ),
                            ]
                        )
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-xl-4 col-md-12">
        @include('website.home.cards.upcomingevents', ['events' => $upcomingEvents])
    </div>

    <div class="col-xl-4 col-md-12">
        @if (count($dinnerforms) > 0)
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-utensils fa-fw me-2"></i>
                    Dinnerform
                </div>
                <div class="card-body">
                    @foreach ($dinnerforms as $dinnerform)
                        @include('dinnerform.includes.dinnerform-block', ['dinnerform' => $dinnerform])
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-bullhorn fa-fw me-2"></i>
                Weekly update
            </div>

            @if ($weekly)
                <div
                    class="card-body overflow-hidden"
                    style="max-height: calc(100vh - 250px)"
                >
                    @if ($weekly->featuredImage)
                        <img
                            width="500px"
                            height="300px"
                            src="{{ $weekly->featuredImage ? $weekly->featuredImage->generateImagePath(500, 300) : null }}"
                            class="img-fluid img-thumbnail mb-3 w-50 mx-auto d-block"
                            alt="Featured image"
                        />
                    @endif

                    {!! Markdown::convert($weekly->content) !!}
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route('news::showWeeklyPreview', ['id' => $weekly->id]) }}"
                        class="btn btn-info btn-block my-2"
                    >
                        Continue reading
                    </a>
                </div>
            @else
                <p class="card-text text-left ms-4 mt-4 mb-4">
                    Weekly update is coming soon...
                </p>
            @endif
        </div>

        @if (isset($videos) && count($videos) > 0)
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    <i class="fab fa-youtube fa-fw me-2"></i>
                    Recent videos
                </div>
                <div class="card-body">
                    @foreach ($videos as $video)
                        @include(
                            'videos.includes.video_block',
                            [
                                'video' => $video,
                                'photo_pop' => false,
                            ]
                        )
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@section('right-column')
    @include('website.home.cards.recentalbums', ['albums' => $albums])

    @parent

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-newspaper fa-fw me-2"></i>
            News
        </div>
        <div class="card-body">
            @if (count($newsitems) > 0)
                @foreach ($newsitems as $index => $newsitem)
                    <div style="max-height: 300px">
                        @include(
                            'website.home.cards.card-bg-image',
                            [
                                'height' => $newsitem->is_weekly ? 80 : 120,
                                'url' => $newsitem->url,
                                'img' => $newsitem->featuredImage
                                    ? $newsitem->featuredImage->generateImagePath(600, 300)
                                    : ($newsitem->is_weekly
                                        ? url('images/weekly-cover.png')
                                        : null),
                                'html' => sprintf(
                                    '<strong>%s</strong><br><em>Published %s</em>',
                                    $newsitem->title,
                                    Carbon::parse($newsitem->published_at)->diffForHumans(),
                                ),
                                'photo_pop' => $newsitem->featuredImage,
                                'leftborder' => 'info',
                            ]
                        )
                    </div>
                @endforeach
            @else
                <p class="card-text text-left ms-1 mt-3 mb-4">
                    No recent news. It's
                    <a
                        href="https://en.wikipedia.org/wiki/Silly_season"
                        target="_blank"
                    >
                        cucumber time
                    </a>
                    . 😴
                </p>
            @endif

            <a
                href="{{ route('news::index') }}"
                class="btn btn-info btn-block"
            >
                View older news
            </a>
        </div>
    </div>
@endsection
