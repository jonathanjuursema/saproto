<div class="card">

    <div class="card-header">
        Committee memberships
    </div>

    <div class="card-body">

        <div class="row">

            @if(count($user->committees) > 0)

                @foreach($user->committees as $committee)
                    <div class="col-md-6 col-xs-12">
                        @include('committee.include.committee_block', [
                         'committee' => $committee,
                         'footer' => sprintf('<strong>%s</strong> %s<br><sup>Since %s</sup>',
                             ($committee->pivot->role ? $committee->pivot->role : 'General Member'),
                             ($committee->pivot->edition ? $committee->pivot->edition : ''),
                             date('j F Y', strtotime($committee->pivot->created_at)))
                        ])
                    </div>
                @endforeach

            @else

                <div class="col-12">

                    <p class="card-text text-center">
                        Currently not a member of a committee.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@if (count($pastcommittees) > 0)

    <div class="card mt-3 mb-3">

        <div class="card-header">
            Past committee memberships
        </div>

        <div class="card-body">

            <div class="row">

                @foreach($pastcommittees as $committeeparticipation)
                    <div class="col-md-6 col-xs-12">
                        @include('committee.include.committee_block', [
                         'committee' => $committeeparticipation->committee,
                         'footer' => sprintf('<strong>%s</strong> %s<br><sup>Between %s and %s</sup>',
                             ($committeeparticipation->role ? $committeeparticipation->role : 'General Member'),
                             ($committeeparticipation->edition ? $committeeparticipation->edition : ''),
                             date('j F Y', strtotime($committeeparticipation->created_at)),
                             date('j F Y', strtotime($committeeparticipation->deleted_at))),
                          'photo_pop' => false
                        ])
                    </div>
                @endforeach

            </div>

        </div>

    </div>

@endif