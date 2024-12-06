@extends('auth.template')

@section('page-title')
    Request Username
@endsection

@section('login-body')

    <form method="POST" action="{{ route("login::requestusername") }}">

        {!! csrf_field() !!}

        <p>Please enter your e-mail address.</p>

        <div class="form-group mb-2">
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}"
                   placeholder="garrus.vakarian@example.com">
        </div>

        <button type="submit" class="btn btn-success btn-block">
            What is my username?
        </button>

    </form>

@endsection
