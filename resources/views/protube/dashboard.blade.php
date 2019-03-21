@extends('website.layouts.redesign.generic')

@section('page-title')
    ProTube Dashboard
@endsection

@section('container')

    <div class="row">

        <div class="col-md-4">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    ProTube settings
                </div>

                <div class="card-body">

                    <p class="card-text">

                        ProTube can keep your history. This means that if you play a song using ProTube, and if you are
                        logged in to the website in that browser, it will remember you put that song in the queue. This
                        enables us to generate a top hits list for you personally, and we have plans to also allow you
                        to sync your own personalized Spotify playlist. None of this information is shared with other
                        people.

                    </p>

                    <hr>

                    <p class="card-text">
                        ProTube is currently <strong>{{$user->keep_protube_history ? '' : 'not' }}</strong> keeping your
                        history.
                    </p>
                    @if($user->keep_protube_history)
                        <a href="{{ route('protube::togglehistory') }}" class="btn btn-outline-danger"
                           style="width: 100%;">
                            Stop keeping my ProTube history.
                        </a>
                    @else
                        <a href="{{ route('protube::togglehistory') }}" class="btn btn-outline-primary"
                           style="width: 100%;">
                            Start keeping my ProTube history.
                        </a>
                    @endif

                    @if($usercount > 0)

                        <hr>

                        <p class="card-text">
                            You have put <strong>{{ $usercount }}</strong> songs in ProTube. You can anonimyze your
                            history. We will keep the songs for historic purposes, but we will remove your name from
                            them. This action is irreversible.
                        </p>

                        <a href="{{ route('protube::clearhistory') }}" class="btn btn-outline-danger"
                           onclick="return confirm('Are you sure?');" style="width: 100%;">
                            Clear my ProTube history.
                        </a>

                    @endif

                </div>

            </div>

            <a href="{{ route('protube::top') }}" class="btn btn-primary btn-block">
                View the public ProTube top hits!
            </a>

        </div>

        <div class="col-md-4">

            <div class="card">

                <div class="card-header bg-dark text-white">
                    Your personal top hits
                </div>

                <div class="card-body" style="max-height: 850px; overflow-y: auto;">

                    @if(count($usertop) == 0)

                        <p class="card-text">
                            There are no videos linked to your account.
                        </p>

                    @else

                        @foreach($usertop as $video)

                            @include('protube.includes.song_block', [
                                'video' => $video
                            ])

                        @endforeach

                    @endif

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card">

                <div class="card-header bg-dark text-white">
                    Recently played
                </div>

                <div class="card-body" style="max-height: 850px; overflow-y: auto;">

                    @foreach($history as $video)

                        @include('protube.includes.song_block', [
                            'video' => $video,
                            'hide_played' => true,
                            'show_text' => sprintf('Played on %s', date('H:i:s', strtotime($video->played_at)))
                        ])

                    @endforeach

                </div>

            </div>

        </div>

    </div>

@endsection