<div class="modal fade" id="changeEmail" tabindex="-1" role="dialog" aria-labelledby="changeEmailLabel">
    <div class="modal-dialog model-sm" role="document">
        <form class="form-horizontal" method="post"
              action="{{ route("user::changemail", ['id' => $user->id]) }}">

            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change email for {{$user->name}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to change the email for {{$user->name}}?<br> <br>
                    <b>Only</b> do this with their consent. <br>
                    Also make sure the request came from them!<br><br>
                    Their new email:
                    <input type="email" class="form-control form-control-sm" id="email" name="email"
                           value="{{ $user->email }}" required>
                    Your password to confirm:
                    <input type="password" class="form-control form-control-sm" id="password" name="password"
                           required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Change {{$user->name}}'s email</button>
                </div>
            </div>
        </form>
    </div>
</div>
