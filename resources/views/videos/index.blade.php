@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Videos Admin
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-2">

            <form method="post" action="{{ route('video::admin::create') }}">

                {!! csrf_field(); !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        Add new video
                    </div>

                    <div class="card-body">
                        <div class="alert alert-danger" role="alert">
                            This feature is currently disabled due to our disabled YouTube API Key.
                        </div>

                        <label>YouTube video ID:</label>
                        <input disabled type="text" class="form-control" name="youtube_id" placeholder="M11SvDtPBhA">
                    </div>

                    <div class="card-footer text-center">
                        <button disabled type="submit" class="btn btn-success btn-block">Add video</button>
                        <br>
                        Developed with
                        <span class="text-danger"><i class="fab fa-youtube fa-fw"></i> YouTube</span>
                    </div>


                </div>

            </form>

        </div>

        <div class="col-md-8">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td>Title</td>
                            <td>Date</td>
                            <td>Length</td>
                            <td>Event</td>
                            <td>Controls</td>

                        </tr>

                        </thead>

                        @foreach($videos as $video)

                            <tr>
                                <td><a href="{{$video->getYouTubeUrl()}}">{{ $video->title }}</a></td>
                                <td>{{ date('d-m-Y', $video->getUnixTimeStamp()) }}</td>
                                <td>{{ $video->getHumanDuration() }}</td>
                                <td>
                                    @if ($video->event)
                                        <a href="{{ route('event::show',['id'=>$video->event->getPublicId()]) }}">
                                            {{  sprintf("%s (%s)",$video->event->title,date('d-m-Y', $video->event->start)) }}
                                        </a>
                                    @else
                                        <i class="opacity-50">none</i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('video::show', ['id' => $video->id]) }}">
                                        <i class="fas fa-play me-2"></i>
                                    </a>
                                    <a href="{{ route('video::admin::edit', ['id' => $video->id]) }}">
                                        <i class="fas fa-edit me-2"></i>
                                    </a>
                                    @include('components.modals.confirm-modal', [
                                       'action' => route('video::admin::delete', ['id' => $video->id]),
                                       'text' => '<i class="fas fa-trash text-danger"></i>',
                                       'title' => 'Confirm Delete',
                                       'message' => "Are you sure you want to delete this video: $video->title",
                                       'confirm' => 'Delete',
                                    ])
                                </td>
                            </tr>

                        @endforeach

                    </table>
                </div>

            </div>

        </div>

    </div>

@endsection
