@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Withdrawal Administration
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a
                        href="{{ route('omnomcom::withdrawal::create') }}"
                        class="badge bg-info float-end"
                    >
                        Create a new withdrawal.
                    </a>
                </div>

                @if (count($withdrawals) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-dark text-white">
                                    <td>#</td>
                                    <td>ID</td>
                                    <td>Withdrawal Date</td>
                                    <td>Users</td>
                                    <td>Orderlines</td>
                                    <td>Sum</td>
                                    <td>Status</td>
                                    <td>View</td>
                                </tr>
                            </thead>
                            @php
                                /** @var \App\Models\Withdrawal $withdrawal */
                            @endphp

                            @foreach ($withdrawals as $withdrawal)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('omnomcom::withdrawal::show', ['id' => $withdrawal->id]) }}"
                                        >
                                            {{ $withdrawal->id }}
                                        </a>
                                    </td>
                                    <td>{{ $withdrawal->withdrawalId }}</td>
                                    <td>{{ $withdrawal->date }}</td>
                                    <td>
                                        {{ $withdrawal->total_users_associated }}
                                    </td>
                                    <td>
                                        {{ $withdrawal->total_orderlines_associated }}
                                    </td>
                                    <td>
                                        &euro;{{ number_format($withdrawal->sum_associated_orderlines, 2, ',', '.') }}
                                    </td>
                                    <td>
                                        {{ $withdrawal->closed ? 'Closed' : 'Pending' }}
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('omnomcom::withdrawal::show', ['id' => $withdrawal->id]) }}"
                                        >
                                            Withdrawal
                                        </a>

                                        /

                                        <a
                                            href="{{ route('omnomcom::withdrawal::showAccounts', ['id' => $withdrawal->id]) }}"
                                        >
                                            Accounts
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="card-footer pb-0">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <p class="text-center">There are no withdrawals.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
