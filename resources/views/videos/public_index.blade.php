@extends('website.layouts.redesign.generic')

@section('page-title')
    Videos
@endsection

@section('container')
    <div class="card">
        <div class="card-body">
            <div class="row">
                @foreach ($videos as $key => $video)
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        @include(
                            'videos.includes.video_block',
                            [
                                'video' => $video,
                            ]
                        )
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
