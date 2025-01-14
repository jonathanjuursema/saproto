@extends('auth.template')

@section('page-title')
    SSO Authentication
@endsection

@section('login-body')
    <form method="POST" action="{{ $destination }}" id="samlform">
        <input type="hidden" name="SAMLResponse" value="{{ $response }}" />
        <input
            type="submit"
            value="Continue Authentication"
            class="btn btn-default btn-block"
        />
    </form>

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        document.getElementById('samlform').submit()
    </script>
@endsection
