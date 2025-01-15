@extends('auth.template')

@section('page-title')
    Redirecting to SurfConext...
@endsection

@section('login-body')
    <p>
        You are being redirected to the SurfConext login page. If you are not
        automatically redirected, please click the button below.
    </p>

    <a href="{{ $url }}" class="btn btn-primary btn-block">
        Click here to login with SurfConext
    </a>
@endsection

@push('javascript')
    <script nonce="{{ csp_nonce() }}">
        window.location.href = '{{ $url }}'
    </script>
@endpush
