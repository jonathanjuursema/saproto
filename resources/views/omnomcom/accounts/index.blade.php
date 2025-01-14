@extends('website.layouts.redesign.dashboard')

@section('page-title')
    OmNomCom Product Accounts
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a
                        href="{{ route('omnomcom::accounts::create') }}"
                        class="badge bg-info float-end"
                    >
                        Create a new account.
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td>Name</td>
                                <td>Acc. Number</td>
                                <td>Associated Prod.</td>
                                <td></td>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($accounts as $account)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('omnomcom::accounts::show', ['id' => $account->id]) }}"
                                        >
                                            {{ $account->name }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $account->account_number }}
                                    </td>
                                    <td>
                                        {{ $account->products->count() }}
                                    </td>
                                    <td style="min-width: 60px">
                                        <a
                                            href="{{ route('omnomcom::accounts::edit', ['id' => $account->id]) }}"
                                        >
                                            <i class="fas fa-edit me-2"></i>
                                        </a>
                                        <a
                                            href="{{ route('omnomcom::accounts::delete', ['id' => $account->id]) }}"
                                        >
                                            <i
                                                class="fas fa-trash text-danger"
                                            ></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
