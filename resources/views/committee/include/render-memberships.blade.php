<div class="card mb-3">
    <div
        class="card-header bg-dark text-white cursor-pointer"
        data-bs-toggle="collapse"
        data-bs-target="#committee_collapse_{{ $unique }}"
    >
        {!! $title !!}

        @if (Auth::user()->can('board') && isset($edition) && isset($edit) && count(array_filter($memberships, fn ($n) => $n->deleted_at == null)) > 0)
            @include(
                'components.modals.confirm-modal',
                [
                    'action' => route('committee::membership::endedition', [
                        'edition' => $edition,
                        'committee' => $committee->id,
                    ]),
                    'text' => "End edition <i class='fas fa-ban'></i>",
                    'title' => 'Confirm the ending of this edition',
                    'message' =>
                        'Are you sure you want to end all memberships in this edition?',
                    'confirm' => 'End',
                    'classes' => 'badge bg-danger float-end mt-1',
                ]
            )
        @endcan
    </div>

    <div
        id="committee_collapse_{{ $unique }}"
        class="collapse {{ $display ? 'show' : null }}"
        data-parent="#committee_collapse"
    >
        <div class="card-body">
            @foreach ($memberships as $i => $membership)
                @include(
                    'committee.include.render-membership',
                    [
                        'membership' => $membership,
                    ]
                )
            @endforeach
        </div>
    </div>
</div>
