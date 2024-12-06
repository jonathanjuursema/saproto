@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ ($card == null ? "Add new RFID card." : "Edit existing RFID card.") }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <form method="post"
                  action="{{ ($card == null ? route("user::rfid::add") : route("user::rfid::update", ['id' => $card->id])) }}"
                  enctype="multipart/form-data">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">Card name:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="My Albert Heijn Bonus Card" value="{{ $card->name ?? '' }}" required>
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">Submit</button>

                        <a href="{{ route("user::dashboard", ['id' => $card->user->id]) }}" class="btn btn-default">Cancel</a>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
