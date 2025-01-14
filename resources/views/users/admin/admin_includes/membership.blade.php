@php
    /** @var \App\Models\User $user */
@endphp

<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        Membership center for
        <strong>{{ $user->name }}</strong>
    </div>

    <div class="card-body">
        <ul class="list-group mb-3">
            <li class="list-group-item list-group-item-dark">
                Membership actions
            </li>

            @if ($user->is_member)
                @if (! $user->member->until)
                    @include(
                        'components.modals.confirm-modal',
                        [
                            'action' => route('user::member::endinseptember', ['id' => $user->id]),
                            'method' => 'POST',
                            'classes' => 'list-group-item text-warning',
                            'text' => 'End membership at the end of September',
                            'message' => "Are you sure you want to end the membership of $user->name at the end of September?",
                        ]
                    )
                    @include(
                        'components.modals.confirm-modal',
                        [
                            'action' => route('user::member::remove', ['id' => $user->id]),
                            'method' => 'POST',
                            'classes' => 'list-group-item text-danger',
                            'text' => 'End membership immediately!',
                            'message' => "Are you sure you want to end the membership of $user->name?",
                        ]
                    )
                    <a
                        href="#"
                        class="list-group-item text-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#setMembershipType"
                    >
                        Change membership type
                    </a>
                    <a
                        href="{{ route('membercard::download', ['id' => $user->id]) }}"
                        target="_blank"
                        class="list-group-item"
                    >
                        Preview membership card
                    </a>
                @else
                    @include(
                        'components.modals.confirm-modal',
                        [
                            'action' => route('user::member::removeend', ['id' => $user->id]),
                            'method' => 'POST',
                            'classes' => 'list-group-item text-danger',
                            'text' => 'Cancel membership removal!',
                            'message' => "Are you sure you do not want to let the membership of $user->name end anymore?",
                        ]
                    )
                @endif

                @include(
                    'components.modals.confirm-modal',
                    [
                        'action' => route('membercard::print', ['id' => $user->id]),
                        'method' => 'POST',
                        'classes' => 'list-group-item',
                        'text' => 'Print membership card',
                        'message' =>
                            "Do you want to print $user->name's card? <br> Card last printed on: " .
                            ($user->member->card_printed_on ?? 'Never printed before'),
                    ]
                )

                <a
                    class="list-group-item"
                    href="{{ route('user::admin::toggle_primary_somewhere_else', ['id' => $user->id]) }}"
                >
                    Is
                    {!! $user->member->is_primary_at_another_association ? '' : '<strong>not</strong>' !!}
                    a primary member at another association.
                </a>
            @else
                <li class="list-group-item">Not a member</li>
                @if ($user->address && $user->completed_profile && $user->signed_membership_form)
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i>
                        Has complete profile
                    </li>
                    <a
                        href="#"
                        class="list-group-item text-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#addMembership"
                    >
                        Make member
                    </a>
                @else
                    <li class="list-group-item">
                        <i class="fas fa-times-circle text-danger"></i>
                        Has not completed profile yet
                    </li>
                @endif
            @endif
        </ul>

        @if ($user->is_member)
            <ul class="list-group mb-3">
                <li class="list-group-item list-group-item-dark">
                    Current Membership
                </li>
                <li class="table-responsiv list-group-item">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <td>Since</td>
                                <td>Type</td>
                                <td class="text-center">Form</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ strtotime($user->member->created_at) > 0 ? date('d-m-Y', strtotime($user->member->created_at)) : 'forever' }}
                                </td>
                                <td>
                                    @if ($user->member->membership_type === \App\Enums\MembershipTypeEnum::LIFELONG)
                                        Lifelong
                                        <i class="fas fa-clock"></i>
                                    @elseif ($user->member->membership_type === \App\Enums\MembershipTypeEnum::HONORARY)
                                        Honorary
                                        <i class="fas fa-trophy"></i>
                                    @elseif ($user->member->membership_type === \App\Enums\MembershipTypeEnum::DONOR)
                                        Donor
                                        <i class="fas fa-hand-holding-usd"></i>
                                    @elseif ($user->member->membership_type === \App\Enums\MembershipTypeEnum::PET)
                                        Pet
                                        <i class="fas fa-paw"></i>
                                    @else
                                            Regular
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($user->member->membershipForm)
                                        <a
                                            class="ms-2"
                                            href="{{ route('memberform::download::signed', ['id' => $user->member->membership_form_id]) }}"
                                        >
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        <i
                                            class="fa fa-file-alt"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="No digital membership form, check the physical archive."
                                        ></i>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                        <trow>
                            @if ($user->member->until)
                                <tr>
                                    <td>
                                        <b>Until:</b>
                                        <i>
                                            {{ Carbon::createFromTimestamp($user->member->until)->format('d M Y') }}
                                        </i>
                                    </td>
                                </tr>
                            @endif
                        </trow>
                    </table>
                </li>
            </ul>
        @endif

        @if ($memberships['pending']->count() > 0)
            <ul class="list-group mb-3">
                <li class="list-group-item list-group-item-dark">
                    Pending Membership(s)
                </li>
                <li class="table-responsive list-group-item">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <td>Since</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberships['pending'] as $membership)
                                <tr>
                                    <td>
                                        {{ strtotime($membership->created_at) > 0 ? date('d-m-Y', strtotime($membership->created_at)) : 'forever' }}
                                    </td>
                                    @if ($membership->membershipForm)
                                        <td>
                                            <a
                                                href="{{ route('memberform::download::signed', ['id' => $membership->membership_form_id]) }}"
                                                class="text-decoration-none"
                                            >
                                                <i
                                                    class="fas fa-download fa-fw me-2 text-info"
                                                    aria-hidden="true"
                                                ></i>
                                            </a>
                                            @include(
                                                'components.modals.confirm-modal',
                                                [
                                                    'action' => route('memberform::delete', [
                                                        'id' => $membership->membership_form_id,
                                                    ]),
                                                    'classes' => 'text-danger',
                                                    'text' => '<i class="fas fa-trash fa-fw me-2 text-danger"></i>',
                                                    'title' => 'Confirm Delete',
                                                    'message' => "Are you sure you want to delete the signed membership form of <i>$user->name</i>? Only delete a signed membership form if the form is invalid or the user does not want to become a member.",
                                                    'confirm' => 'Delete',
                                                ]
                                            )
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </li>
            </ul>
        @endif

        @if ($memberships['previous']->count() > 0)
            <ul class="list-group mb-3">
                <li class="list-group-item list-group-item-dark">
                    Previous Membership(s)
                </li>
                <li class="table-responsive list-group-item">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <td>From</td>
                                <td>Until</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberships['previous'] as $membership)
                                <tr>
                                    <td>
                                        {{ strtotime($membership->created_at) > 0 ? date('d-m-Y', strtotime($membership->created_at)) : 'forever' }}
                                    </td>
                                    <td>
                                        {{ date('d-m-Y', strtotime($membership->deleted_at)) }}
                                    </td>
                                    @if ($membership->membershipForm)
                                        <td>
                                            <a
                                                href="{{ route('memberform::download::signed', ['id' => $membership->membership_form_id]) }}"
                                                class="text-decoration-none"
                                            >
                                                <i
                                                    class="fas fa-download fa-fw me-2 text-info"
                                                    aria-hidden="true"
                                                ></i>
                                            </a>
                                            @include(
                                                'components.modals.confirm-modal',
                                                [
                                                    'action' => route('memberform::delete', [
                                                        'id' => $membership->membership_form_id,
                                                    ]),
                                                    'method' => 'POST',
                                                    'classes' => 'text-danger',
                                                    'text' => '<i class="fas fa-trash fa-fw me-2 text-danger"></i>',
                                                    'title' => 'Confirm Delete',
                                                    'message' => "Are you sure you want to delete the signed membership form of <i>$user->name</i>? Only delete a signed membership form if the form is invalid or the user does not want to become a member.",
                                                    'confirm' => 'Delete',
                                                ]
                                            )
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </li>
            </ul>
        @endif
    </div>
</div>
