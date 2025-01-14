<div class="card mb-3">
    <div class="card-header bg-dark text-white">Society memberships</div>

    <div class="card-body">
        <div class="row">
            @if (count($user->societies) > 0)
                @foreach ($user->societies as $society)
                    <div class="col-md-6 col-xs-12">
                        @include(
                            'committee.include.committee_block',
                            [
                                'committee' => $society,
                                'override_committee_name' => sprintf(
                                    '<strong>%s</strong> %s',
                                    $society->name,
                                    $society->pivot->edition,
                                ),
                                'footer' => sprintf(
                                    '<strong>%s</strong><br><sup>Since %s</sup>',
                                    $society->pivot->role ? $society->pivot->role : 'General Member',
                                    date('j F Y', strtotime($society->pivot->created_at)),
                                ),
                            ]
                        )
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <p class="card-text text-center">
                        Currently not a member of a society.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@if (count($pastsocieties) > 0)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Past society memberships
        </div>

        <div class="card-body">
            <div class="row">
                @foreach ($pastsocieties as $societyparticipations)
                    <div class="col-md-6 col-xs-12">
                        @include(
                            'committee.include.committee_block',
                            [
                                'committee' => $societyparticipations->committee,
                                'override_committee_name' => sprintf(
                                    '<strong>%s</strong> %s',
                                    $societyparticipations->committee->name,
                                    $societyparticipations->edition,
                                ),
                                'footer' => sprintf(
                                    '<strong>%s</strong><br><sup>Between %s and %s</sup>',
                                    $societyparticipations->role
                                        ? $societyparticipations->role
                                        : 'General Member',
                                    date('j F Y', strtotime($societyparticipations->created_at)),
                                    date('j F Y', strtotime($societyparticipations->deleted_at)),
                                ),
                                'photo_pop' => false,
                            ]
                        )
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
