@extends('auth.template')

@section('page-title')
    Authentication
@endsection

@section('login-body')

    <form method="POST" action="{{ route('login::post') }}">

        @csrf

        <a href="{{ route('login::edu') }}" class="btn btn-success btn-block mb-3">
            <i class="fas fa-university me-2"></i> Login with a university account
        </a>

        <p>
            - or -
        </p>

        <input type="text" class="form-control mb-1" id="username" name="email" placeholder="Username or E-mail"
               value="{{ (Session::has('login_username') ? Session::get('login_username') : '') }}">

        <input type="password" class="form-control mb-3" id="password" name="password" placeholder="Password">

        <button type="submit" class="btn btn-success btn-block mb-2">
            <i class="fas fa-unlock me-2"></i> Login with Proto account
        </button>

        <a class="btn btn-outline-secondary btn-block mb-2" href="{{ route('login::register::index') }}">
            <i class="fas fa-user-plus me-2"></i> Create Proto account
        </a>

        <a class="btn btn-outline-secondary btn-block mb-2" href="{{ route('login::requestusername::index') }}">
            <i class="fas fa-question me-2"></i> Forgot your username?
        </a>

        <a class="btn btn-outline-secondary btn-block mb-2" href="{{ route('login::password::reset') }}">
            <i class="fas fa-question me-2"></i> Forgot your password?
        </a>

    </form>
@endsection
