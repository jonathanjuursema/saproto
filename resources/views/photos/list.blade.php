@extends('website.layouts.default-nobg')

@section('page-title')
    Albums
@endsection

@section('content')

    @foreach($albums as $key => $album)
        <div class="col-md-4 col-xs-6">

            <a href="{{ route('photo::album::list', ['id' => $album->id]) }}" class="album-link">
                <div class="album"
                     style="background-image: url('{!! $album->thumb !!}')">
                    <div class="album-name">
                        {{ date('M j, Y', $album->date_taken) }}: {{ $album->name }}
                    </div>
                    @if ($album->private)
                        <div class="photo__hidden">
                            <i class="fa fa-low-vision" aria-hidden="true"></i>
                        </div>
                    @endif
                </div>
            </a>
        </div>
    @endforeach

@endsection


