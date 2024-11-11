@extends('emails.template')

@section('page-title')
    Weekly newsletter
@endsection

@section('body')
    @if($image_url)
        <img src="{{ $image_url }}" style="width: 100%;" alt="featured newsitem image" />
    @endif
    <p>
        Hey {{ $user->calling_name }},
    </p>

    @if($text != '')
        {!! Markdown::convert($text) !!}
    @endif

    <br>

    @if($events->count() > 0)

        @foreach($events as $i => $event)

            @if($i % 2 == 0)
                <table class="table bg-body" style="margin: 0; padding: 0; border: none;"
                       width="100%">
                    @else
                        <table class="table table-light" style="margin: 0; padding: 0; border: none;"
                               width="100%">
                            @endif

                            <tr style="margin: 0; padding: 0; border: none;">

                                <td style="margin: 0; padding: 20px 40px; border: none;">

                                    @if($event->image)
                                        <img src="{{ $event->image->generateImagePath(350,100) }}"
                                             style="width: 100%;" alt="{{$event->title}}'s cover image" />
                                    @endif

                                    <p>
                                        <strong>{{ $event->title }}</strong> @ {{ $event->location }}<br>
                                        {{ date('l d F, H:i', $event->start) }}
                                        - {{ ($event->end - $event->start >= 3600*24 ? date('l d F, H:i', $event->end) : date('H:i', $event->end)) }}
                                    </p>

                                    <p>

                                        {!! Markdown::convert($event->summary) !!}

                                    </p>

                                    @if($event->activity?->participants != 0)

                                        <p>
                                            <strong>Sign-up required!</strong><br>
                                            @if($event->activity->participants > 0)
                                                <i>Places available:</i>
                                                {{ $event->activity->participants }}<br>
                                            @elseif($event->activity->participants == -1)
                                                <i>Places available:</i>
                                                unlimited<br>
                                            @endif
                                            <i>Sign-up before:</i>
                                            {{ date('l d F, H:i', $event->activity->registration_end) }}<br>
                                            <i>Participation:</i>
                                            {!! $event->activity->price > 0 ? '&euro;' . number_format($event->activity->price, 2) : 'Free!' !!}
                                            <br>

                                        </p>

                                    @elseif($event->tickets && count($event->tickets))

                                        <p>
                                            <strong>Ticket purchase required!</strong>
                                        </p>

                                    @else

                                        <p>
                                            <i>No sign-up required.</i>
                                        </p>

                                    @endif

                                    <p>
                                        <a href="{{ route('event::show', ['id' => $event->getPublicId()]) }}">
                                            Learn more! >>
                                        </a>
                                    </p>

                                    @if($event->is_external)

                                        <p>
                                            <i><sub>This activity is not organized by S.A. Proto.</sub></i>
                                        </p>

                                    @endif

                                </td>

                            </tr>

                        </table>

                        @endforeach

                        <p>
                            &nbsp;
                        </p>

                        <p>
                            If anything about the activities is not clear, please let me know!
                        </p>

                    @endif

                    <p>
                        Kind regards,<br>
                        {{ config('proto.internal') }}<br>
                        <em>Officer of Internal Affairs</em>
                    </p>

                    <p>
                        &nbsp;
                    </p>

                    <p>
                        ---
                    </p>

                    <p>
                        <sup style="line-height: 1.5;">
                            You receive this e-mail because you subscribed to the weekly newsletter of S.A. Proto. If
                            you would like to
                            stop receiving this e-mail, please click <a style="color: #00aac0;"
                                                                        href="{{ route('unsubscribefromlist', ['hash' => App\Models\EmailList::generateUnsubscribeHash($user->id, $list->id)]) }}">here</a>.
                        </sup>
                    </p>

                @endsection
