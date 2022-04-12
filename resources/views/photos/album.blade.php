@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $photos->album_title }} ({{ date('M j, Y', $photos->album_date) }})
@endsection

@section('container')

    @if($photos->event !== null)

        <a class="btn btn-info btn-block mb-3" href="{{ route('event::show', ['id'=>$photos->event->getPublicId()]) }}">
            These photos were taken at the event {{ $photos->event->title }}, click here for more info.
        </a>

    @endif

    <div class="card mb-3">

        <div class="card-header bg-dark text-white text-center">
            {{ $photos->album_title }} ({{ date('M j, Y', $photos->album_date) }})
        </div>

        <div class="card-body">

            <div class="row">

                @foreach($photos->photos as $key => $photo)

                    <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                        @include('website.layouts.macros.card-bg-image', [
                        'id' => sprintf('photo_%s', $photo->id),
                        'url' => route("photo::view", ["id"=> $photo->id]),
                        'img' => $photo->getUrlAttribute(),
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
            {{ $photos->photos->links() }}
        </div>

        <div class="card-footer text-center">
            <i class="fas fa-shield-alt fa-fw me-3"></i>
            If there is a photo that you would like removed, please contact
            <a href="mailto:photos&#64;{{ config('proto.emaildomain') }}">
                photos&#64;{{ config('proto.emaildomain') }}.
            </a>
        </div>

    </div>

    &nbsp;

@endsection
