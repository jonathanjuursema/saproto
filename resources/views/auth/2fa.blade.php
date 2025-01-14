@extends('auth.template')

@section('page-title')
    Two Factor Authentication
@endsection

@section('login-body')
    <form method="POST" action="{{ route('login::post') }}" autocomplete="off">
        @csrf

        <div class="form-group">
            <input
                type="text"
                class="form-control"
                id="code"
                name="2fa_totp_token"
                placeholder="Enter the code from your app"
            />
        </div>

        <button type="submit" class="btn btn-success btn-block">Verify</button>
    </form>
@endsection
