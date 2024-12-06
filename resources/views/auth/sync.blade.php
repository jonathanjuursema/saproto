@extends('auth.template')

@section('page-title')
    Password Synchronization
@endsection

@section('login-body')

    <form method="POST" action="{{ route("login::password::sync") }}">

        {!! csrf_field() !!}

        <p>
            Please enter your password below.
        </p>

        <br>

        <div class="form-group">
            <input id="password" type="password" name="password" class="form-control" minlength="8"
                   placeholder="Password">
        </div>

        <button type="submit" class="btn btn-success btn-block">
            Synchronize password for {{ Auth::user()->calling_name }}
        </button>

    </form>

@endsection
