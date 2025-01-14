@extends('emails.template')

@section('body')
    <p>Hey!</p>

    <p>I just ran the membership cleanup program.</p>

    <p>
        <strong>Members who had their membership automatically ended:</strong>
    </p>

    <ul>
        @foreach ($deleted_memberships as $member)
            <li>{{ $member->user->name }}</li>
        @endforeach
    </ul>

    <p>
        Kind regards,
        <br />
        The Membership Terminating Clerk
    </p>
@endsection
