@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Alias Management
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td class="text-end">Alias</td>
                            <td>Destination</td>

                        </tr>

                        </thead>

                        @foreach($aliases as $alias => $destinations)

                            <tr>

                                <td class="text-end">

                                    <strong>{{ $alias }}</strong>
                                    @ {{ Config::string('proto.emaildomain') }}

                                    <a href="{{ route('alias::delete', ['id_or_alias' => $alias]) }}" class="ms-2">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>

                                </td>
                                <td>

                                    @foreach($destinations as $destination)

                                        <a href="{{ route('alias::delete', ['id_or_alias' => $destination->id]) }}"
                                           class="me-2">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>

                                        @if($destination->destination)
                                            {{ $destination->destination }}
                                        @elseif($destination->user)
                                            @if($destination->user->isMember)
                                                <a href="{{ route('user::profile', ['id' => $destination->user->getPublicId()]) }}">
                                                    @endif
                                                    @if($destination->user->trashed())
                                                        <span
                                                            class="text-decoration-line-through">{{ $destination->user->name }}</span>
                                                    @else
                                                        {{ $destination->user->name }}
                                                    @endif
                                                    @if($destination->user->isMember)
                                                </a>
                                            @endif
                                        @else
                                            <i>deleted user</i>
                                        @endif

                                        <br>

                                    @endforeach

                                </td>

                            </tr>

                        @endforeach

                    </table>
                </div>

            </div>

        </div>

        <div class="col-md-2">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    Make changes
                </div>

                <div class="card-body">

                    <p>
                        <a href="{{ route('alias::create') }}" class="form-control btn btn-success">Create a new
                            alias.</a>
                    </p>

                    <p class="text-center">
                        - or -
                    </p>

                    <form method="post" action="{{ route('alias::update') }}">

                        {{ csrf_field() }}

                        <input class="form-control mb-3" name="from" placeholder="old alias name">
                        <input class="form-control" name="into" placeholder="new alias name">

                        <br>

                        <input type="submit" class="form-control btn btn-success" value="Rename">

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection
