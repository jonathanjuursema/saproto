@extends('errors.template')

@section('page-title')
        Error {{ $exception->getStatusCode() }}
@endsection

@section('page-body')
    @if ($exception->getMessage() == null)
        Sorry, but we could not find the page you were looking for.
    @elseif (strpos($exception->getMessage(), 'No query results for model') !== false)
        Sorry, but we could not find the resource you were looking for.
    @else
        {{ $exception->getMessage() }}
    @endif
@endsection
