<div class="row mt-2 d-flex flex-column align-items-center">
    <div class="col-md-6 col-xs-12 card p-4">

        <form class="form-horizontal" method="POST"
              action="{{ $action == 'add' ? route('user::address::add', ['id' => $user->id]) : route('user::address::edit', ['id' => $user->id]) }}">

            {!! csrf_field() !!}

            <p class="text-center">
                Address<br>
            </p>

            <hr>

            <div class="form-group">
                <label for="street">Street</label>
                <input type="text" class="form-control" id="street" name="street" placeholder="Wallaby Way"
                       value="{{ $address->street ?? '' }}" required>
            </div>
            <div class="form-group">
                <label for="number">Number</label>
                <input type="text" class="form-control" id="number" name="number" placeholder="42"
                       value="{{ $address->number ?? '' }}" required>
            </div>
            <div class="form-group">
                <label for="zipcode">ZIP Code</label>
                <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="2003FN"
                       value="{{ $address->zipcode ?? '' }}" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Sydney"
                       value="{{ $address->city ?? 'Enschede' }}" required>
            </div>
            <div class="form-group mb-4">
                <label for="country">Country</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="Australia"
                       value="{{ $address->country ?? 'The Netherlands' }}" required>
            </div>

            <button type="submit" class="btn btn-success btn-block">Save</button>

        </form>

    </div>

</div>