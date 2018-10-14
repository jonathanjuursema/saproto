@extends('website.layouts.panel')

@section('page-title')
    Ticket Admin
@endsection

@section('panel-title')
    {{ ($ticket == null ? "Create new ticket." : "Edit tick " . $ticket->product->name .".") }}
@endsection

@section('panel-body')

    <form method="post"
          action="{{ ($ticket == null ? route("tickets::add") : route("tickets::edit", ['id' => $ticket->id])) }}"
          enctype="multipart/form-data">

        {!! csrf_field() !!}

        <div class="form-group">
            <label for="product">Product:</label>
            @if($ticket)
            &nbsp; <i>{{ $ticket->product->name }}</i>
            @endif
            <select class="form-control product-search" id="product" name="product" {{ $ticket ? '' : 'required' }}></select>
        </div>

        <div class="form-group">
            <label for="event">Event:</label>
            @if($ticket)
                &nbsp; <i>{{ $ticket->event->title }} ({{ $ticket->event->getFormattedDateAttribute()->simple }})</i>
            @endif
            <select class="form-control event-search" id="event" name="event" {{ $ticket ? '' : 'required' }}></select>
        </div>

        <div class="form-group">
            <label for="available_from">Available from:</label>
            <input type="text" class="form-control datetime-picker" id="available_from" name="available_from"
                   value="{{ ($ticket ? date('d-m-Y H:i', $ticket->available_from) : '') }}" required>
        </div>

        <div class="form-group">
            <label for="available_to">Available to:</label>
            <input type="text" class="form-control datetime-picker" id="available_to" name="available_to"
                   value="{{ ($ticket ? date('d-m-Y H:i', $ticket->available_to) : '') }}" required>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox"
                       name="is_members_only" {{ ($ticket && $ticket->members_only ? 'checked' : '') }}>
                Available to members only.
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox"
                       name="is_prepaid" {{ ($ticket && $ticket->is_prepaid ? 'checked' : '') }}>
                This ticket should be prepaid.
            </label>
        </div>

        @endsection

        @section('panel-footer')

            <button type="submit" class="btn btn-success pull-right" style="margin-left: 15px;">Submit</button>

            <a href="{{ route("tickets::list") }}" class="btn btn-default pull-right">Cancel</a>

    </form>

@endsection

@section('javascript')

    @parent

    <script type="text/javascript">
        // Initializes datetimepickers for consistent options
        $('.datetime-picker').datetimepicker({
            icons: {
                time: "far fa-clock",
                date: "fas fa-calendar",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down",
                next: "fas fa-chevron-right",
                previous: "fas fa-chevron-left"
            },
            format: 'DD-MM-YYYY HH:mm'
        });
    </script>

@endsection
