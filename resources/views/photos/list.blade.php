@extends('website.layouts.redesign.generic-sidebar')

@section('page-title')
    Albums
@endsection

@section('container')

    <div class="card mb-3">

        <div class="card-body">

            <div class="row">

                @foreach(Flickr::getAlbums() as $key => $album)

                    <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                        @include('website.layouts.macros.card-bg-image', [
                        'url' => route('photo::album::list', ['id' => $album->id]) ,
                        'img' => $album->thumb,
                        'html' => sprintf('<sub>%s</sub><br>%s<strong>%s</strong>', date("M j, Y", $album->date_taken),
                        $album->private ? '<i class="fas fa-eye-slash mr-1 text-info" data-toggle="tooltip" data-placement="top" title="This album contains photos only visible to members."></i>' : null,
                        $album->name),
                        'photo_pop' => true,
                        'height' => 150
                        ])

                    </div>

                @endforeach

            </div>

        </div>

        <div class="card-footer">
            <a href="mailto:photos&#64;{{ config('proto.emaildomain') }}" class="btn btn-default btn-block">
                <i class="fas fa-shield-alt fa-fw mr-3"></i>
                If there is a photo that you would like removed, please contact photos&#64;{{ config('proto.emaildomain') }}.
            </a>
        </div>

    </div>

@endsection
