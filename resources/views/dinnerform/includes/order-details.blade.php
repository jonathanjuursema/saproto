<div class="row justify-content-center">

    <form method="post" action="{{ route('dinnerform::orderline::store', ['id' => $dinnerform->id]) }}">

        @csrf

        <div class="card mb-3">

            <div class="card-header bg-dark text-white">
                Add an order with us at {{$dinnerform->restaurant}}
                @if(Auth::user()->can('tipcie'))
                    <a href="{{ route('dinnerform::edit', ['id' => $dinnerform->id]) }}"
                       class="btn btn-warning badge float-end">
                        <i class="fas fa-edit me-1"></i> Edit dinnerform
                    </a>
                @endif
                <a href="{{$dinnerform->url}}" target="_blank" class="btn btn-success badge float-end me-3">
                    <i class="fas fa-utensils me-1"></i> {{$dinnerform->restaurant}}'s website
                </a>
                @if($dinnerform->event)
                    <br>
                    <span class="text-muted">
                        <i><small>For the event: {{$dinnerform->event->title}}</small></i>
                    </span>
                @endif
            </div>

            <div class="card-body">
                @if($dinnerform->regular_discount_percentage)
                    <div class="alert alert-primary text-center">
                        This dinnerform has a discount of <strong>{{ $dinnerform->regular_discount_percentage }}
                            %</strong> 🎉
                    </div>
                @endif
                <div class="form-group mb-3">
                    <label for="order">What do you want to order?</label>
                    <input class="form-control" id="order" name="order" type="text" required>
                </div>
                <div class="form-group mb-3">
                    <label for="price">What does that cost?</label>
                    <input class="form-control" id="price" name="price" type="number" min="1" step="any" required>
                    @if($dinnerform->hasDiscount())
                        <small><em>Any discounts will automatically be subtracted.</em></small>
                    @endif
                </div>
                @if(! $dinnerform->event)
                    @include('components.forms.checkbox', [
                        'name' => 'helper',
                        'label' => 'Are you a helper at this event?'
                    ])
                @else
                    <div class="form-group">
                        @if($dinnerform->isHelping())
                            <div class="alert alert-info text-center">
                                You are a helper for this event and therefore receive
                                {{ $dinnerform->regular_discount_percentage ? 'an additional' : 'a' }}
                                discount of <strong>€{{ $dinnerform->helper_discount }}</strong>!
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <input type="submit" class="btn btn-success btn-block" value="Order!">
            </div>

        </div>
    </form>
</div>
