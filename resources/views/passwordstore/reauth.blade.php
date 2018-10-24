@extends('auth.template')

@section('page-title')
    Password Store
@endsection

@section('login-body')

    <form method="POST" action="{{ route('passwordstore::auth') }}">

        {!! csrf_field() !!}

        <input type="password" class="form-control mb-3" id="password" name="password"
               placeholder="Password">

        <button type="submit" class="btn btn-success btn-block">Confirm</button>

    </form>
@endsection