@extends('emails.template')

@section('body')
    <p>
        Dear {{ $user->calling_name }},

        @if ($fee == 'remitted')
            <p>
                This is an e-mail to let you know that for this academic year
                your membership has been waived. Although you are a member of
                S.A. Proto during this academic year, you won't have to pay the
                membership fee. Your membership fee has been waived because
                <strong>{{ $remitted_reason }}</strong>
                .
            </p>
        @else
            <p>
                This is an e-mail to let you know you have been charged the
                membership fee of S.A. Proto. You have been charged the
                membership fee because you are a member of S.A. Proto during
                this academic year.

                @if ($fee == 'regular')
                    <p>
                        You have been charged the regular membership fee of
                        &euro;{{ number_format($fee_amount, 2) }} because we
                        have determined, using information provided to us by the
                        University of Twente, that you are currently studying at
                        the University of Twente, and have studied or are
                        studying Creative Technology or Interaction Technology.
                        You also have not indicated to be a primary member at
                        another study association.
                    </p>
                @else
                    <p>
                        You have been charged the reduced membership fee of
                        &euro;{{ number_format($fee_amount, 2) }} because we
                        have determined, using information provided to us by the
                        University of Twente, you are not a student of the
                        Creative Technology or Interaction Technology programmes
                        at the University of Twente. Or you have indicated to be
                        a primary member at another study association.
                    </p>
                @endif
            </p>
        @endif
    </p>

    <p>
        You will find details about your membership fee in your order history on
        the website.
    </p>

    <p>
        If you believe any of the information above is not correct, please reply
        to this e-mail and let me know. If you wish to stop being a member of
        S.A. Proto please send an e-mail to
        <a href="mailto:secretary@proto.utwente.nl">
            secretary@proto.utwente.nl
        </a>
        .
    </p>

    <p>
        Kind regards,
        <br />
        {{ Config::string('proto.treasurer') }}
        <br />
        <i>On behalf of the board of Study Association Proto</i>
    </p>
@endsection
