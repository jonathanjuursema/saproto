<div class="panel panel-default">
    <div class="panel-heading">
        <strong>Your address information</strong>
    </div>
    <div class="panel-body">

        @if(count($user->address) > 0)

            <div class="row">

                @foreach($user->address as $address)
                    <div class="col-md-6">
                        <div class="panel panel-default">

                            <div class="panel-body">

                                <p>
                                    <strong>{{ $address->street }} {{ $address->number }}</strong>
                                    <br>
                                    {{ $address->zipcode }} {{ $address->city }} ({{ $address->country }})
                                </p>

                            </div>
                            <div class="panel-footer">

                                @if((Auth::user()->can('board') || Auth::id() == $user->id))
                                    <div class="row">
                                        <div class="col-md-4 col-xs-4">
                                            <div class="btn-group btn-group-justified" role="group">
                                                <a class="btn btn-default"
                                                   href="{{ route('user::address::edit', ['address_id' => $address->id, 'id' => $user->id]) }}">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </div>
                                        </div>
                                        @if ($address->is_primary == true)
                                            <div class="col-md-4 col-xs-4">
                                                <div class="btn-group btn-group-justified" role="group">
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-success" disabled>
                                                            <i class="fa fa-star"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($address->is_primary == false)
                                            <div class="col-md-4 col-xs-4">
                                                <form method="POST"
                                                      action="{{ route('user::address::primary', ['address_id' => $address->id, 'id' => $user->id]) }}">
                                                    {!! csrf_field() !!}
                                                    <div class="btn-group btn-group-justified" role="group">
                                                        <div class="btn-group" role="group">
                                                            <button type="submit" class="btn btn-default">
                                                                <i class="fa fa-star"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        @if ($address->is_primary == false || $user->member == null)
                                            <div class="col-md-4 col-xs-4">
                                                <form method="POST"
                                                      action="{{ route('user::address::delete', ['address_id' => $address->id, 'id' => $user->id]) }}">
                                                    {!! csrf_field() !!}
                                                    <div class="btn-group btn-group-justified" role="group">
                                                        <div class="btn-group" role="group">
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fa fa-trash-o"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        @else

            <p style="text-align: center; font-weight: bold;">
                There are currently no addresses linked to your account.
            </p>

        @endif

        <hr>

        <p style="text-align: center;">
            Currently other members are

            <strong>
                @if(!$user->address_visible)
                    not
                @endif
                able
            </strong>

            to see your <strong>primary</strong> address. Click

            <a href="{{ route('user::address::togglehidden', ['id' => $user->id]) }}">
                here
            </a>

            to toggle this behaviour. Your
            secondary addresses are always hidden.
        </p>

    </div>
    <div class="panel-footer">
        <div class="btn-group btn-group-justified" role="group">
            <div class="btn-group" role="group">
                <a type="button" class="btn btn-success" href="{{ route('user::address::add', ['id' => $user->id]) }}">
                    Add another address
                </a>
            </div>
        </div>
    </div>
</div>