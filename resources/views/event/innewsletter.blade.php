@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Edit Newsletter
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="post"
                  action="{{ route("newsletter::text") }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        <p class="card-text text-center">
                            The newsletter was last sent
                            <strong>{{ App\Models\Newsletter::sentAt()->diffForHumans() }}</strong>
                        </p>

                        <button class="btn {{ App\Models\Newsletter::lastSentMoreThanWeekAgo() ? "btn-success" : "btn-danger" }} btn-block"
                                data-bs-toggle="modal" data-bs-target="#sendnewsletter" type="button">
                            {{ (App\Models\Newsletter::lastSentMoreThanWeekAgo() ? 'Send the weekly newsletter!': 'Newsletter already sent this week!') }}
                        </button>

                        <hr>

                        <div class="form-group">
                            <label for="newsletter-text">Text in newsletter</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'text',
                                'placeholder' => 'Text goes here.',
                                'value' => App\Models\Newsletter::text()
                            ])
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">Save text</button>
                        <a class="btn btn-default" target="_blank"
                           href="{{ route("newsletter::preview") }}">
                            Preview
                        </a>
                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    Activities in the newsletter
                </div>

                @if (count($events) > 0)

                    <table class="table table-sm table-hover">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td>Event</td>
                            <td>When</td>
                            <td></td>
                            <td></td>

                        </tr>

                        </thead>

                        @foreach($events as $event)

                            <tr class="{{ $event->include_in_newsletter ? '' : 'opacity-50' }}">

                                <td>{{ $event->title }}</td>
                                <td>{{ $event->generateTimespanText('l j F, H:i', 'H:i', '-') }}</td>
                                <td>
                                    <i class="fas fa-{{ ($event->include_in_newsletter ? 'check' : 'times') }}"
                                       aria-hidden="true"></i>
                                </td>
                                <td>
                                    <a href="{{ route('newsletter::toggle', ['id' => $event->id]) }}">
                                        Toggle
                                    </a>
                                </td>

                            </tr>

                        @endforeach

                    </table>

                @else

                    <div class="card-body">
                        <p class="card-text text-center">
                            There are no upcoming events. Seriously. Go fix that {{ Auth::user()->calling_name }}.
                        </p>
                    </div>

                @endif

            </div>

        </div>

    </div>

    <div id="sendnewsletter" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send the newsletter?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        The newsletter was last sent: <br>
                        <strong>
                            {{ App\Models\Newsletter::sentAt()->diffForHumans() }}
                        </strong>
                    </p>
                    <p>
                        Are you SURE you want to send the newsletter? You should only send the newsletter once per week!
                    </p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('newsletter::send') }}">
                        {!! csrf_field() !!}
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                                class="btn {{ App\Models\Newsletter::lastSentMoreThanWeekAgo() ? "btn-success" : "btn-danger" }}">
                            Send
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection