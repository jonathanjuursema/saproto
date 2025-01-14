@extends('auth.template')

@section('page-title')
    Application Requesting Access
@endsection

@section('login-body')
    <p>
        <strong>{{ $client->name }}</strong>
    </p>

    <p>requests full access your account</p>

    <p>
        <strong>{{ Auth::user()->name }}</strong>
        <br />
        <i>{{ Auth::user()->email }}</i>
    </p>

    <hr />

    <form method="post" action="/oauth/authorize">
        {{ csrf_field() }}

        <input type="hidden" name="state" value="{{ $request->state }}" />
        <input type="hidden" name="client_id" value="{{ $client->id }}" />
        <button type="submit" class="btn btn-success btn-block mt-2">
            Give access
        </button>
    </form>

    <p>- or -</p>

    <form method="post" action="/oauth/authorize">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}

        <input type="hidden" name="state" value="{{ $request->state }}" />
        <input type="hidden" name="client_id" value="{{ $client->id }}" />
        <button class="btn btn-danger btn-block mt-2">Deny access</button>
    </form>
@endsection
