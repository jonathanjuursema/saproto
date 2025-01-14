@extends('auth.template')

@section('page-title')
    Password Reset
@endsection

@section('login-body')
    <form method="POST" action="{{ route('login::password::reset::submit') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $reset->token }}" />

        <p class="mb-3">
            You're resetting the password for:
            <br />
            <strong>{{ $reset->user->name }}</strong>

            <input
                id="password"
                type="password"
                name="password"
                class="form-control mb-3"
                minlength="10"
                placeholder="New password (at least 10 characters)"
            />

            <input
                id="password2"
                type="password"
                name="password_confirmation"
                class="form-control mb-3"
                minlength="10"
                placeholder="New password (again)"
            />

            <button type="submit" class="btn btn-success btn-block">
                Reset password for {{ $reset->user->calling_name }}
            </button>
        </p>
    </form>
@endsection
