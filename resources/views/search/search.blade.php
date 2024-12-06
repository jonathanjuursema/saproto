@extends('website.layouts.redesign.generic')

@section('page-title')
    Search
    @if ($term != null & strlen($term) > 0)
        results for {{ $term }}
    @endif
@endsection

@section('container')

    <div class="row justify-content-center">
        @if (count($users) + count($committees) + count($pages) + count($events) + count($photoAlbums) == 0)

            <div class="col-md-4 col-sm-6 col-xs-10">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text text-center">
                            Your search has returned no results.
                        </p>
                    </div>
                </div>
            </div>

        @endif


        @if(count($photoAlbums)+count($users)>0)
            <div class="col-md-3">
                @if (count($photoAlbums) > 0)
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            Photo Albums
                        </div>
                        <div class="card-body">

                            @foreach($photoAlbums as $album)

                                @include('website.home.cards.card-bg-image', [
                                'url' => route('photo::album::list', ['id' => $album->id]) ,
                                'img' => $album->thumb(),
                                'html' => sprintf('<sub>%s</sub><br><strong>%s</strong>', date("M j, Y", $album->date_taken), $album->name),
                                'leftborder' => 'info'
                                ])

                            @endforeach

                        </div>
                    </div>
                @endif
                @if (count($users) > 0)
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            Proto members
                        </div>
                        <div class="card-body">

                            @foreach($users as $user)

                                @include('users.includes.usercard', [
                                    'user' => $user,
                                    'subtitle' => sprintf('<em>Member since %s</em>',
                                     date('U', strtotime($user->member->created_at)) > 0 ? date('F Y', strtotime($user->member->created_at)) : 'forever!')
                                ])

                            @endforeach

                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if (count($committees) > 0)

            <div class="col-md-3">

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Committees
                    </div>
                    <div class="card-body">

                        @foreach($committees as $committee)

                            @include('committee.include.committee_block', ['committee' => $committee])

                        @endforeach

                    </div>
                </div>

            </div>

        @endif

        @if (count($events) > 0)

            <div class="col-md-3">

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Events
                    </div>
                    <div class="card-body">

                        @foreach($events as $counter=>$event)

                            @include('event.display_includes.event_block', [
                                'event'=> $event,
                                'include_year' => true,
                                'lazyload' => $counter > 6
                            ])

                        @endforeach

                    </div>
                </div>

            </div>

        @endif

        @if (count($pages) > 0)

            <div class="col-md-3">

                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Pages
                    </div>
                    <div class="card-body">

                        @foreach($pages as $page)

                            @include('website.home.cards.card-bg-image', [
                                'url' => route("page::show", ["slug" => $page->slug]),
                                'img' => $page->featuredImage ? $page->featuredImage->generateImagePath(300, 200) : null,
                                'photo_pop' => true,
                                'html' => $page->title,
                                'height' => 100,
                                'leftborder' => 'info'
                            ])

                        @endforeach

                    </div>
                </div>

            </div>

        @endif

    </div>

@endsection
