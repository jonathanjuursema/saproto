@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Generate new withdrawal
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="row-md-4">
            <form
                method="post"
                action="{{ route('omnomcom::withdrawal::store') }}"
            >
                @csrf

                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <p class="card-text text-center">
                            There are currently
                            <strong>
                                {{ \App\Http\Controllers\WithdrawalController::openOrderlinesTotal() }}
                            </strong>
                            unpaid orderlines for a grand total of
                            <strong>
                                &euro;{{ number_format(\App\Http\Controllers\WithdrawalController::openOrderlinesSum(), 2, ',', '.') }}
                            </strong>
                            .

                            @php($wd = Carbon::createFromFormat('Y-m-d', date('Y-m-25')))
                            @include(
                                'components.forms.datetimepicker',
                                [
                                    'name' => 'date',
                                    'label' => 'Withdrawal date:',
                                    'placeholder' => strtotime(
                                        Carbon::now()->day > 20 ? $wd->addMonth() : $wd,
                                    ),
                                    'format' => 'date',
                                ]
                            )
                        </p>

                        <div class="form-group">
                            <label for="name">Maximum amount per user:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">&euro;</span>
                                </div>
                                <input
                                    type="number"
                                    min="0"
                                    class="form-control"
                                    id="max"
                                    name="max"
                                    required
                                />
                                <span class="input-group-text">
                                    ,
                                    <sup>00</sup>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('omnomcom::withdrawal::index') }}"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
