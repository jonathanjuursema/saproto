@extends('website.layouts.redesign.dashboard')

@section('page-title')
    E-mail Administration
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white mb-1">
                    E-mail lists
                    <a
                        href="{{ route('email::list::create') }}"
                        class="badge bg-info float-end"
                    >
                        Create new list.
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td>List name</td>
                                <td>Public</td>
                                <td>Subscribers</td>
                                <td>Controls</td>
                            </tr>
                        </thead>

                        <tr>
                            <td>All members</td>
                            <td></td>
                            <td>
                                {{ App\Models\Member::countValidMembers() }}
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>All pending members</td>
                            <td></td>
                            <td>
                                {{ App\Models\Member::countPendingMembers() }}
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>All active members</td>
                            <td></td>
                            <td>
                                {{ App\Models\Member::countActiveMembers() }}
                            </td>
                            <td></td>
                        </tr>

                        @foreach ($lists as $list)
                            <tr>
                                <td>{{ $list->name }}</td>
                                <td>
                                    {{ $list->is_member_only ? 'Member only' : 'Public' }}
                                </td>
                                <td>{{ $list->users_count }}</td>
                                <td>
                                    <a
                                        href="{{ route('email::list::edit', ['id' => $list->id]) }}"
                                    >
                                        <i class="fas fa-edit me-2"></i>
                                    </a>

                                    @include(
                                        'components.modals.confirm-modal',
                                        [
                                            'action' => route('email::list::delete', ['id' => $list->id]),
                                            'text' => '<i class="fas fa-trash text-danger"></i>',
                                            'title' => 'Confirm Delete',
                                            'message' => "Are you sure you want to delete e-mail list $list->name?",
                                            'confirm' => 'Delete',
                                        ]
                                    )
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            @include(
                'emailadmin.admin_includes.filter',
                [
                    'searchTerm' => $searchTerm ?? null,
                    'description' => $description ?? null,
                    'subject' => $subject ?? null,
                    'body' => $body ?? null,
                ]
            )
        </div>

        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    Emails
                    <a
                        href="{{ route('email::create') }}"
                        class="badge bg-info float-end"
                    >
                        Compose email.
                    </a>
                </div>

                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="bg-dark text-white">
                            <td>Description</td>
                            <td>Recipients</td>
                            <td>Scheduled</td>
                            <td>Status</td>
                            <td>Controls</td>
                        </tr>
                    </thead>

                    @foreach ($emails as $email)
                        <tr class="{{ $email->sent ? 'opacity-50' : '' }}">
                            <td>{{ $email->description }}</td>
                            <td>
                                @if ($email->sent)
                                    Went to {{ $email->sent_to }} people
                                @else
                                    Will go to
                                    {{ $email->recipients()->count() }} people
                                @endif
                                <br />
                                via
                                @if ($email->to_user)
                                    all users
                                @elseif ($email->to_pending)
                                    all pending members
                                @elseif ($email->to_member)
                                    all members
                                @elseif ($email->to_active)
                                    all active members
                                @elseif ($email->to_list)
                                    list(s) {{ $email->getListName() }}
                                @elseif ($email->to_event)
                                    event(s)
                                    {{ $email->to_backup ? 'with backup users' : '' }}:
                                    @foreach ($email->events()->get() as $event)
                                            {{ $event->title }}.
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                {{ date('d-m-Y H:i', $email->time) }}
                            </td>
                            <td>
                                @if (! $email->sent)
                                    @if ($email->ready)
                                        Queued
                                    @else
                                            Draft
                                    @endif
                                @else
                                    <i>Sent</i>
                                @endif
                            </td>
                            <td>
                                <a
                                    href="{{ route('email::show', ['id' => $email->id]) }}"
                                >
                                    <i class="fas fa-eye me-2 text-info"></i>
                                </a>
                                @if (! $email->sent)
                                    @include(
                                        'components.modals.confirm-modal',
                                        [
                                            'action' => route('email::delete', ['id' => $email->id]),
                                            'text' => '<i class="fas fa-trash text-danger me-2"></i>',
                                            'title' => 'Confirm Delete',
                                            'message' => 'Are you sure you want to delete this e-mail?',
                                            'confirm' => 'Delete',
                                        ]
                                    )

                                    @if (! $email->ready)
                                        <a
                                            href="{{ route('email::toggleready', ['id' => $email->id]) }}"
                                        >
                                            <i
                                                class="fas fa-paper-plane text-warning me-2"
                                            ></i>
                                        </a>
                                        <a
                                            href="{{ route('email::edit', ['id' => $email->id]) }}"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('email::toggleready', ['id' => $email->id]) }}"
                                        >
                                            <i
                                                class="fas fa-undo text-info"
                                            ></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>

                <div class="card-footer pb-0">
                    {!! $emails->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
