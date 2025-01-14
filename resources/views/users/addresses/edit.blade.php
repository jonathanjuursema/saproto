@extends('website.layouts.redesign.generic')

@section('page-title')
    Edit address for {{ $user->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    @if (Session::get('wizard'))
                        @include('users.registerwizard_macro')
                    @endif

                    @include('users.addresses.form')
                </div>

                <div class="card-footer">
                    <a
                        href="{{ route('user::dashboard', ['id' => $user->id]) }}"
                        class="btn btn-default"
                    >
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
