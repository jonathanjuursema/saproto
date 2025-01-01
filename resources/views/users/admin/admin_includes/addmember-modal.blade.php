<div class="modal fade" id="addMembership" tabindex="-1" role="dialog" aria-labelledby="addMembershipLabel">
    <div class="modal-dialog" role="document">
        <form action="{{ route("user::member::create", ['id'=>$user->id]) }}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Make member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <ul>
                        @if($user->utwente_username == null)
                            <li class="text-warning">
                                <strong>
                                    {{ $user->calling_name }} has no University of Twente account linked to their user
                                    account. Please kindly ask the user to - if they are a student - link their UTwente
                                    account!
                                </strong>
                            </li>
                        @endif

                        @if (!$user->completed_profile)
                            <li class="text-danger">
                                <strong>
                                    {{ $user->calling_name }} has not yet completed their membership profile. Please ask
                                    them to complete their membership profile before making them a member.
                                </strong>
                            </li>
                        @endif
                        @if (!$user->address)
                            <li class="text-danger">
                                <strong>
                                    {{ $user->calling_name }} does not have an address linked to their account. Please
                                    ask them to register an address before making them a member.
                                </strong>
                            </li>
                        @endif
                        @if (!$user->bank)
                            <li class="text-danger">
                                <strong>
                                    {{ $user->calling_name }} does not have a current bank authorization. This is
                                    required for membership. Please ask them to enter a bank authorization before making
                                    them a member.
                                </strong>
                            </li>
                        @endif
                        @if(!$user->signed_membership_form)
                            <li class="text-danger">
                                <strong>
                                    {{ $user->calling_name }} has not signed the membership form digitally,
                                    make sure they sign it either digitally or physically.
                                </strong>
                            </li>
                        @endif
                    </ul>

                    <p>You are about to make {{ $user->name }} a member.</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                    @if ($user->address && $user->bank && $user->completed_profile)
                        <button type="submit" class="btn btn-success"
                                @if(!$user->signed_membership_form)
                                    onClick="return confirm('Did this user sign the membership form?')
                                @endif
                        ">Make member
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
