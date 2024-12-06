@extends('website.layouts.redesign.dashboard')

@section('page-title')
    @php /** @var \App\Models\Withdrawal $withdrawal */@endphp
    Withdrawal of {{ date('d-m-Y', strtotime($withdrawal->date)) }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <a href="{{ route('omnomcom::withdrawal::index') }}" class="btn btn-block btn-dark mb-2">
                <i class="fas fa-back"></i>
                Return to overview
            </a>

            <form method="post" action="{{ route('omnomcom::withdrawal::edit', ['id' => $withdrawal->id]) }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white mb-2">
                        @yield('page-title')
                    </div>

                    <table class="table table-sm table-borderless ms-3">

                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ $withdrawal->withdrawalId }}</td>
                        </tr>
                        <tr>
                            <th>Users</th>
                            <td>{{ $withdrawal->users_count }}</td>
                        </tr>
                        <tr>
                            <th>Orderlines</th>
                            <td>{{ $withdrawal->orderlines_count }}</td>
                        </tr>
                        <tr>
                            <th>Sum</th>
                            <td>&euro;{{ number_format($withdrawal->total(), 2) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $withdrawal->closed ? 'Closed' : 'Pending' }}</td>
                        </tr>
                        </tbody>

                    </table>

                    <div class="card-body">
                        @include('components.forms.datetimepicker', [
                            'name' => 'date',
                            'label' => 'Change date:',
                            'placeholder' => strtotime($withdrawal->date),
                            'format' => 'date',
                        ])
                        <input type="submit" value="Save" class="btn btn-success btn-block mt-2">
                    </div>

                    <div class="card-footer">

                        <a href="{{ route('omnomcom::withdrawal::export', ['id' => $withdrawal->id]) }}"
                           class="btn btn-outline-success btn-block">
                            Generate XML
                        </a>

                        @if(! $withdrawal->closed)

                            @include('components.modals.confirm-modal', [
                               'action' => route('omnomcom::withdrawal::email', ['id' => $withdrawal->id]),
                               'classes' => 'btn btn-outline-warning btn-block mt-2',
                               'text' => 'E-mail Users',
                               'title' => 'Confirm Send',
                               'message' => 'Are you sure you want to send an email to all '.$withdrawal->users()->count().' users associated with this withdrawal?',
                               'confirm' => 'Send',
                            ])

                            @include('components.modals.confirm-modal', [
                               'action' => route('omnomcom::withdrawal::close', ['id' => $withdrawal->id]),
                               'classes' => 'btn btn-outline-danger btn-block mt-2',
                               'text' => 'Close Withdrawal',
                               'title' => 'Confirm Close',
                               'message' => 'Are you sure you want to close this withdrawal? After closing, you cannot change anything about this withdrawal anymore.',
                               'confirm' => 'Close',
                            ])

                            @include('components.modals.confirm-modal', [
                               'action' => route('omnomcom::withdrawal::delete', ['id' => $withdrawal->id]),
                               'classes' => 'btn btn-outline-danger btn-block mt-2',
                               'text' => 'Delete',
                               'title' => 'Confirm Delete',
                               'message' => 'Are you sure you want to delete this withdrawal?',
                            ])

                        @endif

                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-9">

            <div class="card mb-3">

                <div class="card-header mb-1 bg-dark text-white">
                    Users in this withdrawal
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover">

                        <thead>
                        <tr class="bg-dark text-white">
                            <td>ID</td>
                            <td>User</td>
                            @if(!$withdrawal->closed)
                                <td>Bank Account</td>
                                <td>Authorization</td>
                            @endif
                            <td>#</td>
                            <td>Sum</td>
                            @if(!$withdrawal->closed)
                                <td>Controls</td>
                            @endif
                        </tr>
                        </thead>

                        @foreach($userLines as $data)

                            <tr>
                                <td>{{ $data->user->id }}</td>
                                <td>{{ $data->user->name }}</td>
                                @if(! $withdrawal->closed)
                                    @isset($data->user->bank)
                                        <td>
                                            {{ $data->user->bank->iban }}
                                            <span class="text-muted">/ {{ $data->user->bank->bic }}</span>
                                        </td>
                                        <td>{{ $data->user->bank->machtigingid }}</td>
                                    @else
                                        <td class="text-warning">
                                            <i class="fa fas fa-exclamation-triangle"></i>
                                            <strong>No bank account!</strong>
                                        </td>
                                        <td></td>
                                    @endisset
                                @endif
                                <td>{{ $data->orderline_count }}</td>
                                <td>&euro;{{ number_format($data->total_price, 2, ',', '.') }}</td>
                                @if(!$withdrawal->closed)
                                    <td>
                                        @if($withdrawal->failedWithdrawals->contains('user_id', $data->user->id))
                                            Failed
                                            @include('components.modals.confirm-modal', [
                                               'action' => route('omnomcom::orders::delete', ['id'=>$withdrawal->failedWithdrawals->where('user_id', $data->user->id)->first()->correction_orderline_id]),
                                               'text' => '(Revert)',
                                               'title' => 'Confirm Revert',
                                               'message' => 'Are you sure you want to revert this withdrawal? The user will <b>NOT</b> automatically receive an e-mail about this!',
                                               'confirm' => 'Revert',
                                            ])
                                        @else
                                            <a href="{{ route('omnomcom::withdrawal::deleteuser', ['id' => $withdrawal->id, 'user_id' => $data->user->id]) }}"
                                               class="text-white fw-bold underline-on-hover">
                                                Remove
                                            </a>

                                            |

                                            @include('components.modals.confirm-modal', [
                                               'action' => route('omnomcom::withdrawal::markfailed', ['id' => $withdrawal->id, 'user_id' => $data->user->id]),
                                               'text' => 'Failed',
                                               'title' => 'Confirm Marking Failed',
                                               'message' => 'Are you sure you want to mark this withdrawal for '.$data->user->name.' as failed? They <b>will</b> automatically receive an e-mail about this!',
                                               'classes' => 'text-white fw-bold underline-on-hover'
                                            ])

                                            |

                                            @include('components.modals.confirm-modal', [
                                               'action' => route('omnomcom::withdrawal::markloss', ['id' => $withdrawal->id, 'user_id' => $data->user->id]),
                                               'text' => 'Loss',
                                               'title' => 'Confirm Marking Loss',
                                               'message' => 'Are you sure you want to mark this withdrawal for '.$data->user->name.' as a loss? <b>This cannot easily be undone!</b>',
                                               'classes' => 'text-white fw-bold underline-on-hover'
                                            ])
                                        @endif
                                    </td>
                                @endif
                            </tr>

                        @endforeach

                    </table>
                </div>

            </div>

        </div>

    </div>

@endsection
