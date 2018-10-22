@extends('website.layouts.redesign.generic-sidebar')

@section('page-title')
    {{ $video->title }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-8 col-sm-10 col-xs-12">

            <div class="card">

                <div class="card-header bg-dark text-white">

                    <a href="{{ route('video::index') }}" class="btn btn-info mr-2">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <a href="{{ $video->getYouTubeUrl() }}" class="btn btn-danger" target="_blank">
                        <i class="fab fa-youtube mr-2" aria-hidden="true"></i> {{ $video->youtube_title }} by {{ $video->youtube_user_name }}
                    </a>

                    @if($video->event)
                        <p style="text-align: right; float: right; color: #fff;">


                        </p>

                        <a href="{{ route('event::show',['id'=>$video->event->getPublicId()]) }}" class="btn btn-info float-right">
                            <i class="fas fa-calendar mr-2" aria-hidden="true"></i> {{  sprintf("%s (%s)",$video->event->title,date('d-m-Y', $video->event->start)) }}
                        </a>

                    @endif

                </div>

                <div class="card-body">

                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="{{ $video->getYouTubeEmbedUrl() }}" allowfullscreen></iframe>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection