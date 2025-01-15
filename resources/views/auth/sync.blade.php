@extends('auth.template')

@section('page-title')
    Password Synchronization
@endsection

@section('login-body')
    <form method="POST" action="{{ route('login::password::sync') }}">
        @csrf

        <p>
            Please enter your password below.

            <br />
        </p>

        <div class="form-group">
            <input
                id="password"
                type="password"
                name="password"
                class="form-control"
                minlength="8"
                placeholder="Password"
            />
        </div>

        <button type="submit" class="mt-2 btn btn-success btn-block">
            Synchronize password for {{ Auth::user()->calling_name }}
        </button>
    </form>
@endsection
