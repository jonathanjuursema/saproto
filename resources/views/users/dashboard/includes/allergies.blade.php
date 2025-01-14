<div class="card mb-3">
    <div class="card-header bg-dark text-white">Allergies and diet</div>

    <div class="card-body">
        <p class="card-text">
            @if ($user->hasDiet())
                <em>Your allergies and/or diets are as follows:</em>
                <br />
                {!! Markdown::convert($user->diet) !!}
            @else
                <i>No information provided.</i>
            @endif
        </p>
    </div>

    <div class="card-footer">
        <div
            class="btn btn-outline-info btn-block"
            data-bs-toggle="modal"
            data-bs-target="#diet-modal"
        >
            Update
        </div>
    </div>
</div>

<div id="diet-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="post" action="{{ route('user::diet::edit') }}">
            {{ csrf_field() }}

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Update allergies and/or diet
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">
                    <p>
                        Here you can enter allergies and diets and other info.
                        Everything you enter here will be visible to organizers
                        of activities involving food from the moment you sign up
                        until a week after the activity has ended. The board and
                        website admins will always be able to view this
                        information. Proto will do its best to take your dietary
                        wishes and allergies into account when organsing
                        activities.
                    </p>

                    <p>
                        <strong>
                            However, always remain vigilant if eating the wrong
                            thing can have severe consequences.
                        </strong>
                    </p>

                    <hr />

                    <textarea class="form-control" name="diet">
{{ $user->diet }}</textarea
                    >
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="submit" class="btn btn-outline-info">
                        Save changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
