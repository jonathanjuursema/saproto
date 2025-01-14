<div class="card mb-3 p-0">
    <div class="card-header bg-dark text-white mb-1">
        Activities related to this news
    </div>

    @if (count($upcomingEvents) > 0)
        <table class="table table-sm table-hover">
            <thead>
                <tr class="bg-dark text-white">
                    <td>Event</td>
                    <td>When</td>
                    <td></td>
                </tr>
            </thead>

            @foreach ($upcomingEvents as $event)
                @php($checked = in_array($event->id, $events))
                <tr class="{{ $checked ? '' : 'opacity-50' }}">
                    <td>{{ $event->title }}</td>
                    <td>
                        {{ $event->generateTimespanText('l j F, H:i', 'H:i', '-') }}
                    </td>
                    <td>
                        @include(
                            'components.forms.checkbox',
                            [
                                'name' => 'event[]',
                                'label' => 'Include',
                                'checked' => $checked,
                                'value' => $event->id,
                            ]
                        )
                    </td>
                </tr>
            @endforeach

            <tr>
                <td>
                    <div class="form-group autocomplete">
                        <label for="event">Or search other event(s):</label>
                        <input
                            class="form-control event-search"
                            id="event"
                            name="event[]"
                            multiple
                        />
                    </div>
                </td>
                <td></td>
                <td></td>
            </tr>
        </table>
    @else
        <div class="card-body">
            <p class="card-text text-center">
                There are no upcoming events. Seriously. Go fix that
                {{ Auth::user()->calling_name }}.
            </p>
        </div>
    @endif
</div>
