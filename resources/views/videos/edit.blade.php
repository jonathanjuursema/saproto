@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Edit video
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <form method="post" action="{{ route("video::admin::edit", ['id' => $video->id]) }}" enctype="multipart/form-data">

                {!! csrf_field() !!}

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Video date:</label>
                                @include('website.layouts.macros.datetimepicker', [
                                    'name' => 'video_date',
                                    'format' => 'date',
                                    'placeholder' => strtotime($video->video_date)
                                ])
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Link to event:</label>
                                <select class="form-control event-search" name="event"></select>
                            </div>
                        </div>
                    </div>

                    @if ($video->event)
                        <p style="text-align: center;">
                            Currently linked to:<br>
                            <strong>{{ $video->event->title }} ({{ date('d-m-Y', $video->event->start) }})</strong>
                        </p>
                    @endif

                    <hr>

                    <img src="{{ $video->youtube_thumb_url }}" width="100%">

                </div>

                <div class="card-footer">

                    <button type="submit" class="btn btn-success float-right">Submit</button>

                    <a href="{{ route("video::admin::index") }}" class="btn btn-default">Cancel</a>

                </div>

            </div>

            </form>

        </div>

    </div>

@endsection
