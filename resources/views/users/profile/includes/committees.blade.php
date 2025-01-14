<div class="card mb-3">
    <div class="card-header bg-dark text-white">Committee memberships</div>

    <div class="card-body">
        <div class="row">
            @if (count($user->committees) > 0)
                @foreach ($user->committees as $committee)
                    <div class="col-md-6 col-xs-12">
                        @include(
                            'committee.include.committee_block',
                            [
                                'committee' => $committee,
                                'override_committee_name' => sprintf(
                                    '<strong>%s</strong> %s',
                                    $committee->name,
                                    $committee->pivot->edition,
                                ),
                                'footer' => sprintf(
                                    '<strong>%s</strong><br><sup>Since %s</sup>',
                                    $committee->pivot->role
                                        ? $committee->pivot->role
                                        : 'General Member',
                                    date('j F Y', strtotime($committee->pivot->created_at)),
                                ),
                            ]
                        )
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
        <div class="card-header bg-dark text-white">
            Past committee memberships
        </div>

        <div class="card-body">
            <div class="row">
                @foreach ($pastcommittees as $committeeparticipation)
                    <div class="col-md-6 col-xs-12">
                        @include(
                            'committee.include.committee_block',
                            [
                                'committee' => $committeeparticipation->committee,
                                'override_committee_name' => sprintf(
                                    '<strong>%s</strong> %s',
                                    $committeeparticipation->committee->name,
                                    $committeeparticipation->edition,
                                ),
                                'footer' => sprintf(
                                    '<strong>%s</strong><br><sup>Between %s and %s</sup>',
                                    $committeeparticipation->role
                                        ? $committeeparticipation->role
                                        : 'General Member',
                                    date('j F Y', strtotime($committeeparticipation->created_at)),
                                    date('j F Y', strtotime($committeeparticipation->deleted_at)),
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
