@extends('emails.template')

@section('body')
    <p>Hey {{ $calling_name }}!</p>

    <p>
        There are new submissions waiting for you to review! They are from the
        <a
            href="{{ route('feedback::index', ['category' => $category->url]) }}"
        >
            {{ $category->title }}
        </a>
        board!
    </p>

    <p>
        They are the following:

        @foreach ($unreviewed as $feedback)
            <table
                style="
                    margin: 0;
                    padding: 0;
                    border: none;
                    background-color: #f0f0f0;
                    width: 100%;
                "
            >
                <tr style="margin: 0; padding: 0; border: none">
                    <td style="margin: 0; padding: 10px 20px; border: none">
                        <p>
                            {{ $feedback->feedback }}
                        </p>

                        <hr />
                        <p>
                            <sub>
                                {{ $feedback->user->name }} --
                                {{ $feedback->created_at->format('j M Y, H:i') }}
                            </sub>
                        </p>
                    </td>
                </tr>
            </table>
        @endforeach
    </p>

    <p>
        Kind regards,
        <br />
        The board of Study Association Proto
    </p>
@endsection
