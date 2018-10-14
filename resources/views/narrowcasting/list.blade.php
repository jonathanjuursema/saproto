@extends('website.layouts.default')

@section('page-title')
    Narrowcasting Admin
@endsection

@section('content')

    @if (count($messages) > 0)

        <table class="table">

            <thead>

            <tr>

                <th>#</th>
                <th></th>
                <th>Campaign name</th>
                <th>Start</th>
                <th>End</th>
                <th>Slide duration</th>
                <th>Controls</th>

            </tr>

            </thead>

            @foreach($messages as $message)

                <tr {!! ($message->campaign_end < date('U') ? 'style="opacity: 0.5;"': '') !!}>

                    <td>{{ $message->id }}</td>
                    <td>
                        @if($message->video())
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        @elseif($message->image)
                            <i class="fas fa-picture-o" aria-hidden="true"></i>
                        @endif
                    </td>
                    <td>{{ $message->name }}</td>
                    <td>{{ date('l F j Y, H:i', $message->campaign_start) }}</td>
                    <td>{{ date('l F j Y, H:i', $message->campaign_end) }}</td>
                    <td>
                        @if($message->video())
                            {{ $message->videoDuration() }} seconds
                        @elseif($message->image)
                            {{ $message->slide_duration }} seconds
                        @else
                            <p style="color: red;">no content</p>
                        @endif
                    </td>
                    <td>
                        <a class="btn btn-xs btn-default"
                           href="{{ route('narrowcasting::edit', ['id' => $message->id]) }}" role="button">
                            <i class="fas fa-pencil" aria-hidden="true"></i>
                        </a>
                        <a class="btn btn-xs btn-danger"
                           href="{{ route('narrowcasting::delete', ['id' => $message->id]) }}" role="button">
                            <i class="fas fa-trash-o" aria-hidden="true"></i>
                        </a>
                    </td>

                </tr>

            @endforeach

        </table>

        <p style="text-align: center;">
            <a href="{{ route('narrowcasting::add') }}">Create a new campaign</a> or <a
                    href="{{ route('narrowcasting::clear') }}">delete all past campaigns</a>.
        </p>

    @else

        <p style="text-align: center;">
            There are currently no campaigns to display.
            <a href="{{ route('narrowcasting::add') }}">Create a new campaign.</a>
        </p>

    @endif

@endsection