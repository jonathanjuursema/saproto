@extends('website.master')

@section('page-title')
    Whoops!
@endsection

@section('body')
    <div class="d-flex justify-content-center vh-100">
        <div
            class="col-xl-4 col-lg-8 col-md-8 col-sm-10 col-xs-12 mx-3 text-center"
        >
            <div class="card mb-3 mt-5">
                <div class="card-header text-center bg-dark text-white">
                    S.A. Proto |
                    @yield('page-title')
                </div>

                <div class="card-body text-center">
                    <p>
                        @yield('page-body')
                    </p>

                    <p>
                        <sub>
                            @if (! empty(Sentry::getLastEventID()))
                                This incident has already been reported:
                                #{{ Sentry::getLastEventID() }}
                            @else
                                This incident has
                                <strong>not</strong>
                                been reported.
                            @endif
                        </sub>
                    </p>
                </div>

                <a
                    href="/"
                    class="card-footer text-muted text-center text-decoration-none"
                >
                    Go back to homepage.
                </a>
            </div>

            <img
                src="{{ asset('images/logo/regular.png') }}"
                width="60%"
                class="mb-3 mt-5"
            />
        </div>
    </div>
@endsection
