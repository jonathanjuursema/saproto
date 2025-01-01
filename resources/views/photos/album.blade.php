@extends('website.layouts.redesign.generic')
@php
    /**
     * @var \App\Models\PhotoAlbum $album
     * @var \Illuminate\Support\Collection<\App\Models\Photo> $photos
     */
@endphp
@section('page-title')
    {{ $album->name }} ({{ date('M j, Y', $album->date_taken) }})
@endsection

@section('container')

    @if($album->event)

        <a class="btn btn-info btn-block mb-3" href="{{ route('event::show', ['id'=>$album->event->getPublicId()]) }}">
            These photos were taken at the event {{ $album->event->title }}, click here for more info.
        </a>

    @endif

    <div class="card mb-3">

        <div class="card-header bg-dark text-white text-end">
            <a href="{{route("photo::albums")}}" class="btn btn-success float-start me-3">
                <i class="fas fa-list"></i> <span class="d-none d-sm-inline">Album overview</span>
            </a>
            @can('protography')
                <a href="{{route("photo::admin::edit", ['id' => $album->id])}}"
                   class="btn btn-success float-start me-3">
                    <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Edit album</span>
                </a>
            @endcan
            <div class="p-1 m-1 fw-bold">
                {{ $album->name }} ({{ date('M j, Y', $album->date_taken) }})
            </div>
        </div>

        <div class="card-body">

            <div class="row">

                @foreach($photos as $photo)

                    <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                        @include('website.home.cards.card-bg-image', [
                        'id' => sprintf('photo_%s', $photo->id),
                        'url' => route("photo::view", ["id"=> $photo->id]),
                        'img' => $photo->thumbnail(),
                        'html' => sprintf('<i class="fas fa-heart"></i> %s %s',
                            $photo->getLikes(), $photo->private ?
                            '<i class="fas fa-eye-slash ms-4 me-2 text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="This photo is only visible to members."></i>'
                             : null),
                        'photo_pop' => true,
                        'height' => 200
                        ])

                    </div>

                @endforeach

            </div>

        </div>
        <div class="card-footer">
            {{ $photos->links() }}
        </div>

        <div class="card-footer text-center">
            <i class="fas fa-shield-alt fa-fw me-3"></i>
            If there is a photo that you would like removed, please contact
            <a href="mailto:photos&#64;{{ Config::string('proto.emaildomain') }}">
                photos&#64;{{ Config::string('proto.emaildomain') }}.
            </a>
        </div>

    </div>

    &nbsp;

@endsection
