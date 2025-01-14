<page backtop="15mm" backbottom="15mm" backleft="23mm" backright="23mm">
    <style>
        * {
            box-sizing: border-box;
        }

        h2 {
            margin-bottom: 12mm;
            font-size: 15pt;
        }

        p {
            margin: 5mm auto;
            line-height: 12pt;
            font-size: 10pt;
        }

        li {
            padding-left: 2mm;
        }
    </style>

    <h2>Becoming a member of Study Association Proto</h2>

    @include('users.includes.membershipform_include')

    <div style="height: 30mm">
        <p style="margin-bottom: 0">
            Signature:
            @if ($signature)
                <img src="{{ $signature }}" height="150" />
            @endif
        </p>
    </div>

    <p>
        <strong>{{ $user->name }}</strong>
        <br />
        Enschede, {{ date('F j, Y') }}
    </p>
</page>
