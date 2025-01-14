<div id="product-nav" class="col-10">
    @foreach ($categories as $category)
        <?php $products_in_category = []; ?>

        <div
            class="category-view {{ $category == $categories[0] ? '' : 'inactive' }}"
            data-id="{{ $category->category->id }}"
        >
            <?php /** @var $product \App\Models\Product */
            ?>

            @foreach ($category->products as $product)
                @if ($product->isVisible())
                    <?php
                    if ($product->stock > 0) {
                        $products_in_category[] = $product->id;
                    }
                    ?>

                    <div
                        class="product col-3 {{ $product->stock <= 0 ? 'nostock' : '' }}"
                        data-id="{{ $product->id }}"
                        data-stock="{{ $product->stock }}"
                        data-price="{{ $product->price }}"
                    >
                        <div class="product-inner">
                            <div class="product-image">
                                @if ($product->image)
                                    <div
                                        class="product-image-inner"
                                        style="
                                            background-image: url('{!! $product->image->generateImagePath(100, null) !!}');
                                        "
                                    ></div>
                                @endif
                            </div>

                            <div class="product-details">
                                <div class="product-name">
                                    {{ $product->name }}
                                </div>

                                <div class="product-price">
                                    &euro;
                                    {{ number_format($product->price, 2, '.', '') }}
                                </div>

                                @if ($product->stock < 1000)
                                    <div class="product-stock">
                                        {{ $product->stock }} x
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if (count($products_in_category) > 0)
                <div
                    class="product col-3 random {{ count($products_in_category) <= 1 ? 'nostock' : '' }}"
                    data-list="{{ implode(',', $products_in_category) }}"
                    data-stock="{{ count($products_in_category) }}"
                >
                    <div class="product-inner">
                        <div class="product-image">
                            <div
                                class="product-image-inner"
                                style="
                                    background-image: url('{{ asset('images/omnomcom/dice.png') }}');
                                "
                            ></div>
                        </div>
                        <div class="product-details">
                            <div class="product-name">I'm feeling lucky!</div>

                            <div class="product-price">Who knows?</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    <!-- This is for the minor member tool //-->
    @if (count($minors) > 0)
        <div class="category-view inactive" data-id="static-minors">
            @foreach ($minors as $user)
                <div class="product col-3">
                    <div class="product-inner">
                        <div
                            class="product-image user-image"
                            style="
                                background-image: url('{!! $user->generatePhotoPath(200, 200) !!}');
                            "
                        ></div>
                        <div class="product-details">
                            <div class="product-name">
                                {{ $user->name }}
                            </div>

                            <div class="product-stock user-age">
                                Age: {{ $user->age() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
