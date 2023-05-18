@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($ticket == null ? "Create new ticket." : "Edit ticket " . $ticket->product->name .".") }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="post"
                  action="{{ ($ticket == null ? route("tickets::add") : route("tickets::edit", ['id' => $ticket->id])) }}"
                  enctype="multipart/form-data">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white mb-1">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        {!! csrf_field() !!}

                        <div class="form-group autocomplete">
                            <label for="product">Product:</label>
                            <input class="form-control product-search" id="product" name="product"
                                   placeholder="{{ $ticket ? $ticket->product->name : '' }}"
                                   value="{{ $ticket ? $ticket->product->id : '' }}" {{ $ticket ? '' : 'required' }}>
                        </div>

                        <div class="form-group autocomplete">
                            <label for="event">Event:</label>
                            <input class="form-control event-search" id="event" name="event"
                                   placeholder="{{ $ticket ? $ticket->event->title : '' }}"
                                   value="{{ $ticket ? $ticket->event->id : ''  }}" {{ $ticket ? '' : 'required' }}>
                        </div>

                        @include('components.forms.datetimepicker', [
                            'name' => 'available_from',
                            'label' => 'Available from:',
                            'placeholder' => $ticket ? $ticket->available_from : null
                        ])

                        @include('components.forms.datetimepicker', [
                            'name' => 'available_to',
                            'label' => 'Available to:',
                            'placeholder' => $ticket ? $ticket->available_to : null
                        ])

                        @include('components.forms.checkbox', [
                            'name' => 'is_members_only',
                            'checked' =>  $ticket?->members_only,
                            'label' => 'Available to members only.'
                        ])

                        @include('components.forms.checkbox', [
                            'name' => 'is_prepaid',
                            'checked' =>  $ticket?->is_prepaid,
                            'label' => 'This ticket should be prepaid.'
                        ])

                        @include('components.forms.checkbox', [
                           'name' => 'show_participants',
                           'checked' =>  $ticket?->show_participants,
                           'label' => "Show the participant's who bought this ticket on the event."
                       ])

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">Submit</button>

                        <a href="{{ route("tickets::list") }}" class="btn btn-default">Cancel</a>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection