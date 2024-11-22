@extends('website.layouts.redesign.generic')

@section('page-title')
    Albums
@endsection

@section('container')

    <div class="card mb-3">
        @can('protography')
            <a href="{{route("photo::admin::index")}}" class="btn btn-info">
                <i class="fas fa-edit"></i> <span class="d-none d-sm-inline">Photo admin</span>
            </a>
        @endcan
        <div class="card-body">

            <div class="row">

                @foreach($albums as $key => $album)

                    <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                        @include('website.home.cards.card-bg-image', [
                        'url' => route('photo::album::list', ['id' => $album->id]) ,
                        'img' => $album->thumb(),
                        'html' => sprintf('<sub>%s</sub><br>%s<strong>%s</strong>', date("M j, Y", $album->date_taken),
                        $album->private ? '<i class="fas fa-eye-slash me-1 text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="This album contains photos only visible to members."></i>' : null,
                        $album->name),
                        'photo_pop' => true,
                        'height' => 150
                        ])

                    </div>

                @endforeach

            </div>

        </div>
        <div class="card-footer">
            {{ $albums->links() }}
        </div>

        <div class="card-footer text-center">
            <i class="fas fa-shield-alt fa-fw me-3"></i>
            If there is a photo that you would like removed, please contact
            <a href="mailto:photos&#64;{{ Config::string('proto.emaildomain') }}">
                photos&#64;{{ Config::string('proto.emaildomain') }}.
            </a>
        </div>

    </div>

@endsection
