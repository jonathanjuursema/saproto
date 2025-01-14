@if ($event)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Copy event
            <i
                class="fas fa-info-circle ms-1"
                data-bs-toggle="tooltip"
                data-bs-placement="right"
                title="The event and its signup will be copied starting at the specified date, all the other dates will be moved relatively to the start date!"
            ></i>
        </div>

        <form
            method="post"
            action="{{ route('event::copy', ['id' => $event->id]) }}"
        >
            @csrf

            <div class="card-body">
                @include(
                    'components.forms.datetimepicker',
                    [
                        'name' => 'newDate',
                        'label' => 'This will copy the event and move the start to:',
                        'placeholder' => Carbon::createFromTimestamp($event->start)->addWeek()
                            ->timestamp,
                        'format' => 'date',
                    ]
                )
            </div>

            <div class="card-footer">
                <input
                    type="submit"
                    class="btn btn-success btn-block"
                    value="{{ $event->activity ? 'Copy the event and signup!' : 'Copy the event!' }}"
                />
            </div>
        </form>
    </div>
@endif
