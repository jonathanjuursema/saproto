@extends('auth.template')

@section('page-title')
    QR Code One-Time Authentication
@endsection

@section('login-body')
    <p>
        Are you sure you want to approve
        <br />
        <strong>{{ $description }}</strong>
        ?
    </p>

    <p>
        <a
            href="{{ route('qr::approve', ['code' => $code]) }}"
            class="btn btn-success"
        >
            Approve
        </a>
    </p>
@endsection
