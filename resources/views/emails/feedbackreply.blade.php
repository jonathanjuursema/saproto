@extends('emails.template')

@section('body')
    <p>Hey there {{ $user->calling_name }},</p>

    <p>
        The board saw your feedback and replied! If you have any questions or
        remarks simply reply to this email.
    </p>

    <p>
        You posted the following feedback to the
        {{ $feedback->category->title }} board:
    </p>

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
                    <strong>Votes {{ $feedback->voteScore() }}</strong>
                    <img
                        alt="Thumbs up"
                        style="width: 15px"
                        src="data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZm9jdXNhYmxlPSJmYWxzZSIgZGF0YS1wcmVmaXg9ImZhcyIgZGF0YS1pY29uPSJ0aHVtYnMtdXAiIGNsYXNzPSJzdmctaW5saW5lLS1mYSBmYS10aHVtYnMtdXAgZmEtdy0xNiIgcm9sZT0iaW1nIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJjdXJyZW50Q29sb3IiIGQ9Ik0xMDQgMjI0SDI0Yy0xMy4yNTUgMC0yNCAxMC43NDUtMjQgMjR2MjQwYzAgMTMuMjU1IDEwLjc0NSAyNCAyNCAyNGg4MGMxMy4yNTUgMCAyNC0xMC43NDUgMjQtMjRWMjQ4YzAtMTMuMjU1LTEwLjc0NS0yNC0yNC0yNHpNNjQgNDcyYy0xMy4yNTUgMC0yNC0xMC43NDUtMjQtMjRzMTAuNzQ1LTI0IDI0LTI0IDI0IDEwLjc0NSAyNCAyNC0xMC43NDUgMjQtMjQgMjR6TTM4NCA4MS40NTJjMCA0Mi40MTYtMjUuOTcgNjYuMjA4LTMzLjI3NyA5NC41NDhoMTAxLjcyM2MzMy4zOTcgMCA1OS4zOTcgMjcuNzQ2IDU5LjU1MyA1OC4wOTguMDg0IDE3LjkzOC03LjU0NiAzNy4yNDktMTkuNDM5IDQ5LjE5N2wtLjExLjExYzkuODM2IDIzLjMzNyA4LjIzNyA1Ni4wMzctOS4zMDggNzkuNDY5IDguNjgxIDI1Ljg5NS0uMDY5IDU3LjcwNC0xNi4zODIgNzQuNzU3IDQuMjk4IDE3LjU5OCAyLjI0NCAzMi41NzUtNi4xNDggNDQuNjMyQzQ0MC4yMDIgNTExLjU4NyAzODkuNjE2IDUxMiAzNDYuODM5IDUxMmwtMi44NDUtLjAwMWMtNDguMjg3LS4wMTctODcuODA2LTE3LjU5OC0xMTkuNTYtMzEuNzI1LTE1Ljk1Ny03LjA5OS0zNi44MjEtMTUuODg3LTUyLjY1MS0xNi4xNzgtNi41NC0uMTItMTEuNzgzLTUuNDU3LTExLjc4My0xMS45OTh2LTIxMy43N2MwLTMuMiAxLjI4Mi02LjI3MSAzLjU1OC04LjUyMSAzOS42MTQtMzkuMTQ0IDU2LjY0OC04MC41ODcgODkuMTE3LTExMy4xMTEgMTQuODA0LTE0LjgzMiAyMC4xODgtMzcuMjM2IDI1LjM5My01OC45MDJDMjgyLjUxNSAzOS4yOTMgMjkxLjgxNyAwIDMxMiAwYzI0IDAgNzIgOCA3MiA4MS40NTJ6Ij48L3BhdGg+PC9zdmc+"
                    />
                </p>

                <p>
                    {{ $feedback->feedback }}
                </p>

                <hr />
                <p>
                    <sub>
                        {{ $user->name }} --
                        {{ $feedback->created_at->format('j M Y, H:i') }}
                    </sub>
                </p>
            </td>
        </tr>
    </table>

    <p>And the board {{ $accepted ? 'liked it!' : 'replied with:' }}</p>

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
                    {{ $reply }}
                </p>

                <hr />
                <p>
                    <sub>Board of S.A. Proto -- {{ date('j M Y, H:i') }}</sub>
                </p>
            </td>
        </tr>
    </table>

    <p>
        <sup style="line-height: 1.5">
            You receive this e-mail because you posted something on the
            <a
                href="{{ route('feedback::index', ['category' => $feedback->category->url]) }}"
            >
                {{ $feedback->category->title }} board
            </a>
            .
        </sup>
    </p>
@endsection
