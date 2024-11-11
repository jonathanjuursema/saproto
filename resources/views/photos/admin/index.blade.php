@extends('website.layouts.redesign.generic')

@section('page-title')
    Photo Admin
@endsection

@section('container')

    <div class="row">
        <div class="col-lg-3">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white text-center">
                    Albums
                </div>

                <div class="card-body">

                    <form method="post" action="{{ route('photo::admin::index') }}"
                          class="form-main">
                        {{ csrf_field() }}
                        <div class="input-group">
                            <input class="form-control"
                                   value="{{ $query ?? '' }}"
                                   placeholder="Search albums" type="search" name="query">
                            <button type="submit" class="input-group-text btn btn-info">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <div class="card mb-3">

                <div class="card-header bg-dark text-white text-center">
                    Add Album
                </div>

                <form method="post" action="{{ route('photo::admin::create') }}">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Album name:</label>
                            <input required type="text" id="name" name="name" class="form-control">
                        </div>
                        @include('components.forms.datetimepicker', [
                            'name' => 'date',
                            'label' => 'Album date:',
                            'placeholder' => strtotime(Carbon::now())
                        ])
                        @include('components.forms.checkbox', [
                            'name' => 'private',
                            'label' => 'Private album'
                        ])
                    </div>

                    <div class="card-footer">
                        <input type="submit" class="btn btn-success btn-block" value="Add Album">
                    </div>
                </form>

            </div>

        </div>

        <div class="col-lg-9">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white text-center">
                    Unpublished albums
                </div>

                <div class="card-body">


                    <div class="row"></div>

                    <div class="row">


                        @foreach($unpublished as $album)

                            <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                                @include('website.home.cards.card-bg-image', [
                                'url' => route('photo::admin::edit', ['id' => $album->id]) ,
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

            </div>
            <div class="card mb-3">
                <div class="card-header bg-dark text-white text-center">
                    Published albums
                </div>

                <div class="card-body">


                    <div class="row"></div>

                    <div class="row">

                        @foreach($published as  $album)

                            <div class="col-lg-2 col-lg-3 col-md-4 col-sm-6">

                                @include('website.home.cards.card-bg-image', [
                                'url' => route('photo::admin::edit', ['id' => $album->id]) ,
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

            </div>
        </div>
    </div>

@endsection
