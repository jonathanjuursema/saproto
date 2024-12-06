@if($user->achievements->count()>0)

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Unlocked achievements ({{$user->achievements->count()}})
        </div>

        <div class="card-body">

            @foreach($user->achievements as $key => $achievement)

                @include('achievement.includes.achievement_include', [
                'achievement' => $achievement,
                'include_delete_for' => (Auth::check() && Auth::user()->can("board") ? $user : null),
                'footer' => sprintf("Achieved on %s.", $achievement->pivot->created_at->format('d/m/Y')),
                'obtained'=>$achievement->pivot,
                ])

            @endforeach

        </div>

    </div>

@endif
