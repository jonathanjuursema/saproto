<!DOCTYPE html>
<html lang="en">
<head>

    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,700' rel='stylesheet' type='text/css'>

    <meta name="viewport" content="width=900, user-scalable=no">

    <title>OmNomCom Inventory Viewer</title>

    <meta name="theme-color" content="#0089FA">

    <meta property="og:type" content="website" />
    <meta property="og:title" content="OmNomCom Inventory Viewer" />
    <meta property="og:description"
          content="The OmNomCom Inventory Viewer can tell you if your favourite nom is available in the Protopolis. Give it a try!" />
    <meta property="og:url" content="https://www.omnomcom.nl/" />
    <meta property="og:image" content="{{ asset('images/subsites/omnomcom.jpg') }}" />

    <link rel="shortcut icon" href="{{ asset('images/favicons/favicon'.mt_rand(1, 4).'.png') }}" />

    <style>
        html {
            box-sizing: border-box;
            font: 14px sans-serif;
        }

        body {
            background-color: #0089FA;
            font-family: "Roboto Slab", "Arial", sans-serif;
            color: #fff;
            font-weight: 400;
            font-size: 14px;
        }

        .d-none {
            display: none;
        }

        .animate {
            transition: all 0.5s;
            -o-transition: all 0.5s;
            -moz-transition: all 0.5s;
            -webkit-transition: all 0.5s;
        }

        #mainscreen {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        #mainscreen {
            text-align: center;
            color: #fff;
            font-size: 50px;
            padding-top: 100px;
            font-weight: 700;
        }

        #search_query {
            background-color: #fff;
            font-size: 30px;
            padding: 20px;
            color: #0089FA;
            font-family: "Roboto Slab", "Arial", sans-serif;
            border: none;
            margin-top: 20px;
            width: 800px;
        }

        #results {
            margin: 50px auto 100px auto;
            width: 800px;
            overflow: auto;
        }

        .result {
            background-color: #fff;
            color: #000;
            width: 390px;
            height: 200px;
            margin-bottom: 20px;
            float: left;
            font-weight: 400;
            font-size: 25px;
        }

        .result.unavailable {
            opacity: 0.5;
        }

        .result_left {
            margin-right: 20px;
        }

        .result_image, .result_info {
            width: 235px;
            height: 200px;
            float: left;
        }

        .result_image {
            width: 115px;
            height: 160px;
            margin: 20px;
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .result_name {
            background-color: #0089FA;
            color: #fff;
            padding: 5px;
            margin: 10px;
            font-size: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .result_price, .result_amount {
            width: 100px;
            height: 100px;
            float: left;
        }

        .result_price {
            margin-right: 15px;
            margin-left: 10px;
        }

        .result_title {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 10px 0px;
            text-align: center;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .result_category {
            float: left;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 6px 0px;
            text-align: center;
            font-size: 15px;
            margin: 0px 10px;
            width: 215px;
        }
    </style>

</head>
<body>
<div id="mainscreen" class="animate">
    What would you like to eat?<br>
    <input type="text" id="search_query" name="query">

    <div id="results">
        @foreach(App\Models\Product::where('is_visible', true)
                ->where(function ($query) {
                    $query->where('is_visible_when_no_stock', true)
                    ->orWhere('stock','>',0);
                })
                ->get() as $i => $product)

            @if($product->categories->whereIn('id', Config::array('omnomcom.stores.protopolis.categories'))->count() > 0)

                <div class='result d-none {{ ($product->stock <= 0 ? 'unavailable' : '') }}'>
                    <div class='result_image'
                         @if($product->image)
                             style='background-image:url("{{ $product->image->generateImagePath(400,null) }}")'
                        @endif
                    ></div>
                    <div class='result_info'>
                        <div class='result_name'>{{ $product->name }}</div>
                        <div class='result_price'>
                            <div class='result_title'>Price</div>
                            <div class='result_data'>&euro;{{ number_format($product->price, 2, '.', ',') }}</div>
                        </div>
                        <div class='result_amount'>
                            <div class='result_title'>Available</div>
                            <div class='result_data'>{{ $product->stock }}</div>
                        </div>
                        <div
                            class='result_category'>{{ implode(", ", $product->categories->pluck('name')->toArray()) }}</div>
                    </div>
                </div>

            @endif

        @endforeach
    </div>

    <a href="https://www.proto.utwente.nl/">
        <img src="{{ asset('images/logo/inverse.png') }}" style="width: 400px;">
    </a>

</div>

<script type="text/javascript" nonce="{{ csp_nonce() }}">
    const search = document.getElementById('search_query');
    const results = Array.from(document.getElementsByClassName('result'));
    search.addEventListener('keyup', _ => {
        let query = search.value;
        results.forEach(el => el.classList.replace('result_left', 'd-none'));
        if (query.length > 2) {
            results.forEach(el => el.classList.replace('result_left', 'd-none'));
            let c = 1;
            results.forEach(el => {
                let name = el.querySelector('.result_name').innerHTML.toLowerCase().replace(/[^a-zA-Z0-9]/g, '');
                let category = el.querySelector('.result_category').innerHTML.toLowerCase().replace(/[^a-zA-Z0-9]/g, '');
                let queryMod = query.toLowerCase().replace(/[^a-zA-Z0-9]/g, '');
                if (name.indexOf(queryMod) > -1 || category.indexOf(queryMod) > -1) {
                    el.classList.remove('d-none');
                    if ((c) % 2 === 1) el.classList.add('result_left');
                    c++;
                }
            });
        }
    });
</script>

</body>
</html>
