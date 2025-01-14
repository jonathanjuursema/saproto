@extends('website.layouts.redesign.generic')

@section('page-title')
    Become a member of S.A. Proto!
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    <p class="card-text">
                        Hi there! We're glad that you want to become a member of
                        our association! Before we can make your membership
                        official, we need some more information. Please follow
                        the steps below :)
                    </p>

                    <p class="card-text">
                        If you have any problems, don't hesitate to come by in
                        the Protopolis (Zilverling A230), and ask one of
                        <a
                            href="{{ route('page::show', ['slug' => 'board']) }}"
                        >
                            our board members
                        </a>
                        to help you.
                    </p>
                </div>

                <ul class="list-group list-group-flush">
                    @if (sizeof($todo) > 0)
                        <div class="list-group-item">
                            <strong>To Do</strong>
                        </div>

                        @foreach ($todo as $item)
                            @include('users.becomeamember_include', $item)
                        @endforeach
                    @endif

                    @if (sizeof($done) > 0)
                        <div class="list-group-item">
                            <strong>Done</strong>
                        </div>

                        @foreach ($done as $item)
                            @include('users.becomeamember_include', $item)
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection
