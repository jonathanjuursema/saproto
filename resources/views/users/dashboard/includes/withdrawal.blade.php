@if($user->bank != null)

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            SEPA direct withdrawal
        </div>

        <div class="card-body">

            <p class="card-text text-center">
                You have issued us an authorisation for:<br>
                <strong
                    class="me-5">{{ iban_to_human_format($user->bank->iban) }}</strong>{{ iban_to_human_format($user->bank->bic) }}
            </p>

            <table class="table table-borderless table-sm text-muted mb-0">
                <tbody>
                <tr>
                    <th>Type</th>
                    <td>Recurring</td>
                </tr>
                <tr>
                    <th>Issued on</th>
                    <td>{{ $user->bank->created_at }}</td>
                </tr>
                <tr>
                    <th>Authorisation reference</th>
                    <td>{{ $user->bank->machtigingid }}</td>
                </tr>
                <tr>
                    <th>Creditor identifier</th>
                    <td>{{ Config::string('proto.sepa_info.creditor_id') }}</td>
                </tr>
                </tbody>
            </table>

        </div>

        <div class="card-footer">

            <div class="btn-group btn-block">

                @if(!$user->is_member)

                    <a class="btn btn-outline-danger w-50" data-bs-toggle="modal"
                       data-bs-target="#bank-modal-cancel">
                        Cancel authorization
                    </a>

                @endif

                <a class="btn btn-outline-info w-50" href="{{ route("user::bank::edit") }}">
                    Update authorization
                </a>

            </div>

        </div>

    </div>

@else

    <a type="submit" class="btn btn-outline-info btn-block mb-3" href="{{ route("user::bank::create") }}">
        Issue SEPA direct withdrawal authorisation
    </a>

@endif

@if($user->bank != null)
    <div id="bank-modal-cancel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel withdrawal authorisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    @if($user->hasUnpaidOrderlines())
                        <p class="text-danger">
                            You have unpaid orderlines. You cannot revoke your authorization until you have settled all
                            your purchases with Proto. You can await the next withdrawal, or head over to your
                            <a href="{{ route("omnomcom::orders::index") }}">purchase history</a> to pay manually via
                            iDeal.
                        </p>
                        <hr>
                    @endif

                    <p>
                        This action will cancel your current automatic withdrawal authorization. Everything bought
                        up until now will still be withdrawn from this bank account, but no more purchases can be
                        made using this authorization.
                    </p>

                    <p>
                        After all outstanding balance with this authorization has been paid, the authorization will
                        be permanently deleted. If you wish to make purchases in the future, you can always add a
                        new authorization.
                    </p>

                </div>
                <form method="POST" action="{{ route('user::bank::delete') }}">
                    @csrf
                    <div class="modal-footer">

                        <div class="btn-group btn-block">
                            <button type="submit" class="btn btn-danger w-50"
                                    @disabled($user->hasUnpaidOrderlines()) }}>
                                Cancel my authorization
                            </button>
                            <button type="button" class="btn btn-default w-50" data-bs-dismiss="modal">
                                Keep my authorization active
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
