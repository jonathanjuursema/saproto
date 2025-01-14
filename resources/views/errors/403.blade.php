@extends('errors.template')

@section('page-title')
    Unauthorised
@endsection

@section('page-body')
    @if (isset($exception) && ! empty($exception->getMessage()))
        {{ $exception->getMessage() }}
    @else
            You are not allowed to access this page.
    @endif
@endsection
