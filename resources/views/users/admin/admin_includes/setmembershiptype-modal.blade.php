<div
    class="modal fade"
    id="setMembershipType"
    tabindex="-1"
    role="dialog"
    aria-labelledby="setMembershipTypeLabel"
>
    <div class="modal-dialog model-sm" role="document">
        <form
            action="{{ route('user::member::settype', ['id' => $user->id]) }}"
            method="post"
        >
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set membership type</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure you want to change the membership type of
                        {{ $user->name }}?

                        <span class="me-1">Membership type:</span>
                        <select
                            class="form-select px-2 py-1"
                            aria-label="Membership types"
                            name="type"
                        >
                            <option>Regular member</option>
                            <option
                                @selected($user->member->membership_type === \App\Enums\MembershipTypeEnum::HONORARY)
                                value="honorary"
                            >
                                Honorary member
                            </option>
                            <option
                                @selected($user->member->membership_type === \App\Enums\MembershipTypeEnum::LIFELONG)
                                value="lifelong"
                            >
                                Lifelong member
                            </option>
                            <option
                                @selected($user->member->membership_type === \App\Enums\MembershipTypeEnum::DONOR)
                                value="donor"
                            >
                                Donor
                            </option>
                            <option
                                @selected($user->member->membership_type === \App\Enums\MembershipTypeEnum::PET)
                                value="pet"
                            >
                                Pet member
                            </option>
                        </select>
                    </p>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Set membership type
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
