<page>
    <style>
        * {
            box-sizing: border-box;
        }

        .half {
            margin-left: 10mm;
            margin-right: 10mm;
            height: 49%;
            text-align: center;
        }

        p {
            margin: 10mm auto;
        }

        h3 {
            margin-bottom: -20px;
        }

        .barcode {
            width: 75mm;
            height: 10mm;
        }

        #protologo {
            width: 40%;
        }
    </style>

    <div class="half" style="border-bottom: 3px dashed #000000">
        <barcode class="barcode" value="{{ $ticket->barcode }}"></barcode>

        <h3>{{ $ticket->ticket->product->name }}</h3>
        <p>
            for the
            <strong>{{ $ticket->ticket->event->title }}</strong>
            @if ($ticket->ticket->event->committee)
                <br />
                <sub>
                    organized by the
                    <strong>
                        {{ $ticket->ticket->event->committee->name }}
                    </strong>
                </sub>
            @endif
        </p>

        <p>
            <strong>{{ $ticket->user->name }}</strong>
            <br />
            {{ $ticket->user->getDisplayEmail() }}
        </p>

        <p>
            <sub>#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</sub>
            <br />
            <barcode class="barcode" value="{{ $ticket->barcode }}"></barcode>
        </p>

        <p>
            <sup>
                Please
                <strong>print</strong>
                this ticket and take it with you to the event. Scanning from
                mobile phones may work but might be difficult. If you want to
                show this ticket on your mobile phone, turn up the brightness of
                your screen to the maximum possible.
            </sup>
        </p>
    </div>

    <div class="half">
        <img
            style="margin-top: 30mm"
            src="{{ str_replace('https', 'http', public_path('images/logo/regular.png')) }}"
            id="protologo"
            alt="proto logo"
        />

        <p style="margin-top: 0">
            <sub>
                the ticketing system is proudly presented to you by the
                <br />
                <strong>
                    Have You Tried Turning It Off And On Again committee
                </strong>
            </sub>
        </p>
    </div>
</page>
