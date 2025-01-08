@extends('auth.template')

@section('page-title')
    Password Reset
@endsection

@section('login-body')

    <form method="POST" action="{{ route("login::password::reset::send") }}">

        @csrf

        <p>
            Please enter your e-mail address.
        </p>

        <input id="email" type="email" name="email" class="form-control mb-2" value="{{ old('email') }}"
               placeholder="garrus.vakarian@example.com">

        <button type="submit" class="btn btn-success btn-block">
            Send password reset link
        </button>

        <hr>

        <a class="btn btn-outline-secondary btn-block" href="https://tap.utwente.nl/tap/pwman/index.php"
           target="_blank">
            Reset UTwente password
        </a>

    </form>

@endsection
