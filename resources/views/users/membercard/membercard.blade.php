<page>

    <style>
        html, body {
            margin: 0;
            padding: 0;
        }

        #card {
            position: relative;
            width: 100%;
            height: 99%;
            font-family: Arial, sans-serif;
        }

        #name {
            margin-top: 4mm;
            font-size: 18px;
            font-weight: bold;
            margin-right: 30mm;
        }

        #photo {
            position: absolute;
            width: 25mm;
            height: 25mm;
            top: 8mm;
            right: 5mm;
        }

        #details {
            position: absolute;
            top: 7mm;
            left: 0mm;
            color: #000;
            font-size: 8px;
        }

        #details .heading {
            color: #aaa;
            margin-top: 1mm;
            text-transform: uppercase;
            font-size: 6px;
        }

        #barcode {
            width: 35mm;
            height: 5mm;
            margin-top: 1.5mm;
        }
    </style>
    <div id="card">
        @if(!$overlayonly)
            <div id="details">
                <div id="name">{{ $user->name }}</div>
                <div class="heading">Member since</div>
                <div>{{ ($user->member->created_at->timestamp < 0 ? 'Before we kept track!' : date('F j, Y', strtotime($user->member->created_at))) }}</div>
                <div class="heading">Card validity</div>
                <div>{{ date('M Y') }} - {{ date('M Y', strtotime('+3 years')) }}</div>
                <barcode id="barcode" label="none" value="{{ $user->id }}"></barcode>
            </div>

            @if($user->photo)
                <img id="photo" src="data:image/webp;base64,{{ $user->photo->getBase64(450,450) }}">
            @else
                <img id="photo" src="{{ public_path('images/default-avatars/other.png') }}">
            @endif
        @endif
    </div>

</page>
