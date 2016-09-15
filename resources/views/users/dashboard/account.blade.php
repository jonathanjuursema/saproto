<form class="form-horizontal" method="post" action="{{ route("user::dashboard", ["id" => $user->id]) }}">

    <div class="panel panel-default">

        <div class="panel-heading">
            <strong>Update account</strong>
        </div>

        <div class="panel-body">

            {!! csrf_field() !!}
            <div class="form-group">
                <label for="email" class="col-sm-4 control-label">E-mail</label>

                <div class="col-sm-8">
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="phone" class="col-sm-4 control-label">Phone</label>

                <div class="col-sm-8">
                    <input type="phone" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">

                    <div class="checkbox">
                        <label>
                            <input name="phone_visible"
                                   type="checkbox" {{ ($user->phone_visible == 1 ? 'checked' : '') }}>
                            Show to members
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="receive_sms"
                                   type="checkbox" {{ ($user->receive_sms == 1 ? 'checked' : '') }}>
                            Receive text messages
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="website" class="col-sm-4 control-label">Homepage</label>

                <div class="col-sm-8">
                    <input type="text" class="form-control" id="website" name="website"
                           value="{{ $user->website }}">
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-sm-4 control-label">New Password</label>

                <div class="col-sm-8">
                    <input type="password" class="form-control" id="newpassword" name="newpassword">
                    <input type="password" class="form-control" id="newpassword2" name="newpassword2">
                </div>
            </div>

            <div class="form-group">
                <label for="old_pass" class="col-sm-4 control-label">Password</label>

                <div class="col-sm-8">
                    <input type="password" class="form-control" id="old_pass" name="old_pass"
                           placeholder="For changing e-mail or password">
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <div class="btn-group btn-group-justified" role="group">
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-success">
                        Update account
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>