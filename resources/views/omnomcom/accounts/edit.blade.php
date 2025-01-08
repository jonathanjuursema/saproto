@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($account == null ? "Create new account." : "Edit account " . $account->account_number .".") }}
@endsection

@section('container')

    <form method="post"
          action="{{ ($account == null ? route("omnomcom::accounts::store") : route("omnomcom::accounts::update", ['id' => $account->id])) }}"
          enctype="multipart/form-data">

        @csrf

        <div class="row justify-content-center">

            <div class="col-md-3">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">Account name:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Magical Accessories" value="{{ $account->name ?? '' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="account_number">Account number:</label>
                            <input type="number" class="form-control" id="account_number" name="account_number"
                                   placeholder="1234" value="{{ $account->account_number ?? '' }}" required>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">Submit</button>
                        <a href="{{ route("omnomcom::accounts::index") }}" class="btn btn-default">Cancel</a>
                    </div>

                </div>

            </div>

        </div>

    </form>

@endsection
