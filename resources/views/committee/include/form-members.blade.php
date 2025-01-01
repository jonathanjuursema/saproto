@if(!$new)

    <form class="form-horizontal" action="{{ route('committee::membership::store') }}" method="post">

        @csrf

        <div class="card mb-3">

            <div class="card-header bg-dark text-white">
                Add a member to this committee
            </div>

            <div class="card-body">

                <div class="form-group">
                    <div class="form-group autocomplete">
                        <label for="member">Member</label>
                        <input id="member" class="form-control user-search" name="user_id" required/>
                    </div>
                    <input type="hidden" name="committee_id" value="{{ $committee->id }}">
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input id="role" type="text" class="form-control" name="role" placeholder="Developer">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="edition">Edition</label>
                            <input id="edition" type="text" class="form-control" name="edition" placeholder="3.0">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        @include('components.forms.datetimepicker', [
                            'format' => 'date',
                            'name' => 'start',
                            'label' => 'Since'
                        ])
                    </div>
                    <div class="col-6">
                        @include('components.forms.datetimepicker', [
                            'format' => 'date',
                            'name' => 'end',
                            'label' => 'Until',
                            'not_required' => true
                        ])
                    </div>
                </div>
            </div>

            <div class="card-footer">

                <button type="submit" class="btn btn-success btn-block float-end">
                    Add
                </button>

            </div>

        </div>

    </form>

@endif
