@extends('website.layouts.redesign.generic')

@section('page-title')
    Clear membership profile
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form
                method="POST"
                action="{{ route('user::memberprofile::clear') }}"
            >
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        @csrf

                        <p>
                            If you confirm below, the following information will
                            be deleted:
                        </p>

                        <ul>
                            <li>
                                Birthdate:
                                <strong>
                                    {{ date('F j, Y', strtotime($user->birthdate)) }}
                                </strong>
                            </li>
                            <li>
                                Phone:
                                <strong>{{ $user->phone }}</strong>
                            </li>
                        </ul>

                        <p>Are you sure?</p>
                    </div>

                    <div class="card-footer">
                        <button
                            type="submit"
                            class="btn btn-outline-primary float-end"
                        >
                            Yes, clear my profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
