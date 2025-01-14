@extends('website.layouts.redesign.generic')

@section('page-title')
    ProTube Hits
@endsection

@section('container')
    <div class="btn text-center btn-block mb-3">
        <i class="fas fa-bolt me-3"></i>
        ProTube is developed with
        <span class="text-danger">
            <i class="fab fa-youtube fa-fw"></i>
            YouTube
        </span>
        and
        <span class="text-primary">
            <i class="fab fa-spotify fa-fw"></i>
            Spotify
        </span>
        .

        <i
            class="fab fa-spotify fa-fw ms-3 text-primary"
            aria-hidden="true"
        ></i>
        <a
            href="https://open.spotify.com/user/studyassociationproto/playlist/213N4HAIKNZe7H0X3R79I2"
            target="_blank"
            class="text-primary"
        >
            Get the ProTube Hits Spotify playlist!
        </a>
    </div>

    <div class="row mb-3">
        @foreach ($data as $period => $content)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        @if ($period == 'alltime')
                            All-time
                        @elseif ($period == 'month')
                            Last Months
                        @elseif ($period == 'week')
                            Last Weeks
                        @endif
                        Top 10
                    </div>

                    <div class="card-body">
                        @if (count($content) == 0)
                            <p class="card-text">
                                This list is currently empty.
                            </p>
                        @else
                            @foreach ($content as $video)
                                @include(
                                    'protube.includes.song_block',
                                    [
                                        'video' => $video,
                                    ]
                                )
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    &nbsp;
@endsection
