@extends('errors.template')

@section('page-title')
        Error {{ $exception->getStatusCode() }}
@endsection

@section('page-body')
    {{ $exception->getMessage() }}
    <br />
    <small>
        If you see this error, please check out
        <a
            href="https://wiki.proto.utwente.nl/_media/ict/csrf_token_mismatch_write-up.pdf"
        >
            this manual
        </a>
        on how to possibly fix it.
    </small>
@endsection
