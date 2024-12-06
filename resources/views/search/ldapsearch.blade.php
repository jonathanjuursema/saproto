@extends('website.layouts.redesign.generic')

@section('page-title')
    UTwente Address Book Search
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-xl-3 col-lg-12">

            <form method="post" action="{{ route('search::ldap::post') }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        Search for University of Twente people
                    </div>

                    <div class="card-body">
                        <input type="text" name="query" class="form-control"
                               placeholder="Search for something!" value="{{ $term }}">
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-block">Search</button>
                    </div>

                </div>
            </form>

        </div>

        @if($term)

            <div class="col-xl-9 col-lg-12">

                <div class="card">

                    <div class="card-header bg-dark text-white">
                        Search results
                    </div>

                    <div class="card-body">

                        @if (count($data) <= 0)

                            <p class="text-center card-text">
                                Your search returned no results.
                            </p>

                        @else

                            <div class="row">

                                @foreach($data as $result)

                                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">

                                        <div class="card h-100 leftborder leftborder-info">

                                            <div class="card-body">

                                                <p class="card-text">

                                                    <i class="fas fa-id-badge fa-fw"></i>
                                                    {{ $result['givenname'] ?? null }}
                                                    {{ $result['middlename'] ?? null }}
                                                    {{ $result['sn'] ?? null }},
                                                    {{ $result['initials'] ?? null }}
                                                    <br>

                                                    <i class="fas fa-user-friends fa-fw"></i>
                                                    <em>
                                                        @if(\Illuminate\Support\Str::startsWith($result['description'], 'Student'))
                                                            Student {{ $result['department'] ?? null }}
                                                        @elseif(\Illuminate\Support\Str::startsWith($result['description'], 'Employee'))
                                                            Employee {{ $result['department'] ?? null }}
                                                        @else
                                                            Functional Account
                                                            {{ $result['department'] ? '('.$result->department.')' : null }}
                                                        @endif
                                                    </em>

                                                    @if(Auth::user()->can('board') &&   array_key_exists('userprincipalname', $result))
                                                        <br>
                                                        <i class="fas fa-users-cog fa-fw"></i>
                                                        {{ $result['userprincipalname'] }}
                                                    @endif
                                                    @if(array_key_exists('mail', $result))
                                                        <br>
                                                        <i class="fas fa-envelope fa-fw"></i>
                                                        <a href="mailto:{{ $result['mail'] }}">{{ $result['mail'] }}</a>
                                                    @endif
                                                    @if(array_key_exists('telephonenumber', $result))
                                                        <br>
                                                        <i class="fas fa-phone fa-fw"></i>
                                                        <a href="tel:{{ $result['telephonenumber'] }}">{{ $result['telephonenumber'] }}</a>
                                                    @endif
                                                    @if(array_key_exists('physicaldeliveryofficename', $result))
                                                        <br>
                                                        <i class="fas fa-map-marker-alt fa-fw"></i>
                                                        Room {{ $result['physicaldeliveryofficename'] }}
                                                    @endif

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @endif

    </div>

@endsection
