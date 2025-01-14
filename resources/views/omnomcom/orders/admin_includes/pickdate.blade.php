<form method="get" action="{{ route('omnomcom::orders::filter::date') }}">
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Orderline for specific day
        </div>
        <div class="card-body">
            @include(
                'components.forms.datetimepicker',
                [
                    'name' => 'date',
                    'format' => 'date',
                    'placeholder' => strtotime(date('Y-m-d')),
                ]
            )
        </div>
        <div class="card-footer">
            <input
                type="submit"
                class="btn btn-success btn-block mb-2"
                value="Get orders"
            />
            <a
                href="{{ route('omnomcom::orders::adminlist') }}"
                class="btn btn-success btn-block"
            >
                Today
            </a>
        </div>
    </div>
</form>
