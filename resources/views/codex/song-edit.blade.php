@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Edit Song
@endsection

@section('container')
    <form action="{{ !empty($song) ? route('codexSong.update', ['codexSong' => $song]) : route("codexSong.store") }}"
          method="POST">
        <input type="hidden" name="_method" value="{{ !empty($song) ? "PUT" : "POST" }}">
        {{ csrf_field()}}
        <div class="row gap-3 justify-content-center">
            <div class="col-6">
                <div class="row">
                    <div class="card mb-3">
                        <div class="card-header">
                            Song
                        </div>
                        <div class="card-body">
                            {{-- Title }--}}
                            <label for="type">Title:</label>
                            <div class="form-group mb-3">
                                <input type="text" value="{{$song->title??""}}" class="form-control" id="title"
                                       name="title">
                            </div>

                            {{-- Artist }--}}
                            <label for="artist">Artist:</label>
                            <div class="form-group mb-3">
                                <input type="text" value="{{$song->artist??""}}" class="form-control" id="artist"
                                       name="artist">
                            </div>

                            {{-- Youtube ID }--}}
                            <label for="youtube">Youtube ID:</label>
                            <div class="form-group mb-3">
                                <input type="text" value="{{$song->youtube??""}}" class="form-control" id="youtube"
                                       name="youtube">
                            </div>

                            {{-- Categories }--}}
                            <label for="category">Category</label>

                            <select class="form-select form-select-m mb-3" aria-label="category-dropdown"
                                    name="category">
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}"
                                            {{ $category->id === $myCategories ? "selected" : "" }}>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            {{-- Lyrics }--}}
                            <label for="lyrics">Lyrics:</label>
                            <div class="form-group mb-3">
                                @include('components.forms.markdownfield', [
                               'name' => 'lyrics',
                                   'placeholder' => "Awesome lyrics here!",
                                   'value' => $song->lyrics??"",
                               ])
                            </div>

                            <button type="submit" class="btn btn-success btn-block">
                                Save song!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
