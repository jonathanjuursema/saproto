@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Narrowcasting Admin
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a class="badge bg-info float-end" href="{{ route('narrowcasting::create') }}">
                        Create a new campaign.</a>
                    <a class="badge bg-danger float-end me-2" href="{{ route('narrowcasting::clear') }}">
                        Delete all past campaigns.</a>
                </div>

                <div class="table-responsive">

                    <table class="table table-hover table-sm">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td></td>
                            <td>Campaign name</td>
                            <td>Start</td>
                            <td>End</td>
                            <td>Slide duration</td>
                            <td>Controls</td>

                        </tr>

                        </thead>

                        @foreach($messages as $message)

                            <tr {!! ($message->campaign_end < date('U') ? 'style="opacity: 0.5;"': '') !!}>

                                <td>
                                    @if($message->youtube_id)
                                        <i class="fab fa-youtube" aria-hidden="true"></i>
                                    @elseif($message->image)
                                        <i class="fas fa-picture-o" aria-hidden="true"></i>
                                    @endif
                                </td>
                                <td style="overflow-wrap: break-word; max-width: 160px">{{ $message->name }}</td>
                                <td>{{ date('l F j Y, H:i', $message->campaign_start) }}</td>
                                <td>{{ date('l F j Y, H:i', $message->campaign_end) }}</td>
                                <td>
                                    @if($message->image || $message->youtube_id)
                                        {{ $message->slide_duration }} seconds
                                    @else
                                        <p class="text-danger">no content!</p>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('narrowcasting::edit', ['id' => $message->id]) }}">
                                        <i class="fas fa-edit me-2" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('narrowcasting::delete', ['id' => $message->id]) }}">
                                        <i class="fas fa-trash text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                            </tr>

                        @endforeach

                    </table>

                </div>

                <div class="card-footer pb-0">
                    {{ $messages->links() }}
                </div>

            </div>

        </div>

    </div>

@endsection
