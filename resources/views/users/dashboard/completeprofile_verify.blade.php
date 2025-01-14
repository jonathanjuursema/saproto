@extends('website.layouts.redesign.generic')

@section('page-title')
    Complete membership profile
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="POST" action="{{ route('user::memberprofile::complete') }}">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">@yield('page-title')</div>

                    <div class="card-body">

                        @include('users.registerwizard_macro')

                        @csrf

                        <input type="hidden" name="verified" value="true">

                        <p class="text-center">
                            Please check that you've entered the information below correctly.
                        </p>

                        <hr>

                        <p class="text-center">
                            My date of birth is <strong>{{ date('F j, Y', strtotime($userdata['birthdate'])) }}</strong>
                            ({{ $age }}
                            years).
                        </p>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-outline-primary btn-block">
                            The information above is correct.
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
