@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Overview of Mollie Transactions
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Transactions</div>

                @if (count($transactions) > 0)
                    <div class="card-body">
                        @if ($user)
                            <p>
                                Showing transactions for
                                <strong>{{ $user->name }}</strong>
                                . (
                                <a
                                    href="{{ route('omnomcom::mollie::index') }}"
                                >
                                    Show all
                                </a>
                                )
                            </p>
                        @else
                            <p>
                                <strong>
                                    Search for transactions for specific user:
                                </strong>
                            </p>

                            <p></p>
                            <form method="get">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group autocomplete">
                                            <input
                                                class="form-control user-search"
                                                name="user_id"
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input
                                            type="submit"
                                            class="btn btn-success"
                                            value="Search"
                                        />
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>

                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td></td>
                                <td>Amount</td>
                                <td>User</td>
                                <td>Status</td>
                                <td>Date</td>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td class="text-end">
                                        <a
                                            href="{{ route('omnomcom::mollie::status', ['id' => $transaction->id]) }}"
                                        >
                                            #{{ $transaction->id }}
                                        </a>
                                    </td>

                                    <td>
                                        <strong>&euro;</strong>
                                        {{ number_format($transaction->amount, 2, '.', '') }}
                                    </td>

                                    <td>
                                        <a
                                            href="{{ route('user::admin::details', ['id' => $transaction->user->id]) }}"
                                        >
                                            {{ $transaction->user->name }}
                                        </a>
                                    </td>

                                    <td>
                                        {!! App\Models\MollieTransaction::translateStatus($transaction->translatedStatus()) == 'open' ? '<i class="fas fa-spinner ml-2 text-normal"></i>' : '' !!}
                                        {!! App\Models\MollieTransaction::translateStatus($transaction->translatedStatus()) == 'failed' ? '<i class="fas fa-times ml-2 text-danger"></i>' : '' !!}
                                        {!! App\Models\MollieTransaction::translateStatus($transaction->translatedStatus()) == 'paid' ? '<i class="fas fa-check ml-2 text-success"></i>' : '' !!}
                                        {!! App\Models\MollieTransaction::translateStatus($transaction->translatedStatus()) == 'unknown' ? '<i class="fas fa-question ml-2 text-normal"></i>' : '' !!}
                                        <span class="label label-default">
                                            - {{ $transaction->status }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ date('Y-m-d', strtotime($transaction->created_at)) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="card-footer pb-0">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="card-body">
                        <p class="card-text text-center">
                            There's no transactions.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @if (! $user)
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white mb-1">
                        Account overview
                    </div>

                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td>Month</td>
                                <td class="text-end">Total</td>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($m = 0; $m <= 11 ; $m++)
                                <?php
                                $month = strtotime(sprintf('-%s months', $m));
                                $total = \App\Http\Controllers\MollieController::getTotalForMonth(
                                    date('Y-m', $month),
                                );
                                ?>

                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('omnomcom::mollie::monthly', ['month' => date('Y-m', $month)]) }}"
                                        >
                                            <span class="gray">
                                                {{ date('F Y', strtotime(sprintf('-%s months', $m))) }}
                                            </span>
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total > 0)
                                            <span class="label label-success">
                                                &euro;
                                                {{ number_format($total, 2) }}
                                            </span>
                                        @else
                                            <span class="label label-default">
                                                no transactions
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
