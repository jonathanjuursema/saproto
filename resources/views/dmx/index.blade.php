@extends('website.layouts.redesign.dashboard')

@section('page-title')
    DMX Fixtures
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a href="{{ route('dmx::add') }}" class="badge badge-info float-right">
                        Create a new fixture.
                    </a>
                </div>

                @if (count($fixtures) > 0)

                    <table class="table table-sm table-hover">

                        <thead>

                        <tr class="bg-dark text-white">

                            <td></td>
                            <td>Name</td>
                            <td>First channel</td>
                            <td>Last channel</td>
                            <td colspan="4">Properties</td>
                            <td></td>

                        </tr>

                        </thead>

                        @foreach($fixtures as $fixture)

                            <tr>

                                <td>
                                    @if($fixture->follow_timetable)
                                        <i class="fas fa-calendar fa-fw"></i>
                                    @else
                                        <i class="fas fa-users-cog fa-fw"></i>
                                    @endif
                                </td>
                                <td>{{ $fixture->name }}</td>
                                <td>{{ $fixture->channel_start }}</td>
                                <td>{{ $fixture->channel_end }}</td>
                                <td>
                                    @if(count($fixture->getChannels('red')) > 0)
                                        <span style="color: red;">
                                            <i class="fas fa-tint" aria-hidden="true"></i>
                                            {{ implode(", ", $fixture->getChannels('red')->pluck('id')->toArray()) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(count($fixture->getChannels('green')) > 0)
                                        <span style="color: green;">
                                            <i class="fas fa-tint" aria-hidden="true"></i>
                                            {{ implode(", ", $fixture->getChannels('green')->pluck('id')->toArray()) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(count($fixture->getChannels('blue')) > 0)
                                        <span style="color: blue;">
                                            <i class="fas fa-tint" aria-hidden="true"></i>
                                            {{ implode(", ", $fixture->getChannels('blue')->pluck('id')->toArray()) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(count($fixture->getChannels('brightness')) > 0)
                                        <i class="fas fa-sun" aria-hidden="true"></i>
                                        {{ implode(", ", $fixture->getChannels('brightness')->pluck('id')->toArray()) }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dmx::edit', ['id' => $fixture->id]) }}">
                                        <i class="fas fa-edit mr-2" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('dmx::delete', ['id' => $fixture->id]) }}">
                                        <i class="fas fa-trash text-danger" aria-hidden="true"></i>
                                    </a>
                                </td>

                            </tr>
                        @endforeach

                    </table>

                @else
                    <div class="card-body">
                        <p class="card-text text-center">
                            There are no configured fixtures.
                        </p>
                    </div>
                @endif

            </div>

        </div>

    </div>

@endsection