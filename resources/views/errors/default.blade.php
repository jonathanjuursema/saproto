@extends('errors.template')

@section('page-title')
        Error {{ $exception->getStatusCode() }}
@endsection

@section('page-body')
    @if (! $hide_message)
        {{ $exception->getMessage() }}
    @else
        Oopsie woopsie, website brokey wokey.
        <br />
        <br />
        If you think you shouldn't get this error, please create an issue on
        <a href="https://github.com/saproto/saproto/issues/new">GitHub</a>
        including the URL of this page ({{ Request::url() }}).
    @endif
@endsection
