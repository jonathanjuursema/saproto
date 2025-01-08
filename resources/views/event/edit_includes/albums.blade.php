@if($event)

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Photo albums
        </div>

        <form method="post" action="{{ route('event::linkalbum', ['event'=> $event->id]) }}">

            @csrf

            <div class="card-body">

                @if(count($event->albums) > 0)
                    <p class="card-text">
                        Currently linked albums:
                    </p>
                @endif

                @foreach($event->albums as $album)
                    <span class="badge bg-primary">
                    {{ $album->name }} <a
                                href="{{ route('event::unlinkalbum', ['album'=>$album->id]) }}">
                            <i class="fas fa-times ms-2 text-white"></i>
                        </a>
                    </span>
                @endforeach

                @if(count($event->albums) > 0)
                    <hr>
                @endif

                @if(\App\Models\PhotoAlbum::whereNull('event_id')->count() > 0)

                    <select name="album_id" class="form-control" required>
                        @foreach(\App\Models\PhotoAlbum::whereNull('event_id')->orderBy('date_taken', 'desc')->get() as $album)
                            <option value="{{ $album->id }}">{{ date('Y-m-d', $album->date_taken) }}
                                : {{ $album->name }}</option>
                        @endforeach
                    </select>

                @else

                    <p class="card-text">

                        There are no albums that can be linked to this event.

                    </p>

                @endif

            </div>

            @if(\App\Models\PhotoAlbum::whereNull('event_id')->count() > 0)

                <div class="card-footer">

                    <input type="submit" class="btn btn-success btn-block" value="Link photo album!">

                </div>

            @endif

        </form>

    </div>

@endif
