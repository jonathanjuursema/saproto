@extends('website.layouts.panel')

@section('page-title')
    Account Re-activation
@endsection

@section('panel-title')
    Re-activating an account
@endsection

@section('panel-body')

    <form method="POST" action="{{ route('user::admin::restore', ['id'=>$user->id]) }}">

        {!! csrf_field() !!}

        <div class="form-group">
            <label for="email" class="control-label">Your e-mail address:</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="j.doe@student.utwente.nl"
                   value="{{ (Session::has('register_persist') ? Session::get('register_persist')['email'] : $user->email) }}"
                   required>
        </div>

        <hr>

        <div class="form-group">
            <div class="row">
                <div class="col-md-5">
                    <label for="name" class="control-label">Your full name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="John Doe"
                           value="{{ (Session::has('register_persist') ? Session::get('register_persist')['name'] : $user->name) }}"
                           required>
                </div>
                <div class="col-md-5">
                    <label for="calling_name" class="control-label">Calling name</label>
                    <input type="text" class="form-control" id="calling_name" name="calling_name" placeholder="Johnny"
                           value="{{ (Session::has('register_persist') ? Session::get('register_persist')['calling_name'] : $user->calling_name) }}"
                           required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-5">
                    <label for="birthdate" class="control-label">Birthdate</label>
                    <input type="text" class="form-control datetime-picker" id="birthdate" name="birthdate"
                           placeholder="2011-04-20"
                           value="{{ (Session::has('register_persist') ? Session::get('register_persist')['birthdate'] : '') }}"
                           required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-5">
                    <label for="phone" class="control-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+31534894423"
                           value="{{ (Session::has('register_persist') ? Session::get('register_persist')['phone'] : '') }}"
                           required>
                </div>
            </div>
        </div>

        <hr>

        @endsection

        @section('panel-footer')
            <button type="submit" class="btn btn-success pull-right">REACTIVATE ACCOUNT</button>

    </form>
@endsection

@section('javascript')

    @parent

    <script type="text/javascript">
        // Initializes datetimepickers for consistent options
        $('.datetime-picker').datetimepicker({
            icons: {
                time: "fas fa-clock-o",
                date: "fas fa-calendar",
                up: "fas fa-arrow-up",
                down: "fas fa-arrow-down",
                next: "fas fa-chevron-right",
                previous: "fas fa-chevron-left"
            },
            format: 'YYYY-MM-DD'
        });
    </script>

@endsection