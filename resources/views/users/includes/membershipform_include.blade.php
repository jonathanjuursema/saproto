<p>The undersigned ...</p>
<p>
    @php
        /** @var User $user */
        $date = new DateTime($user->birthdate);
    @endphp

    <b>{{ $user->name }}</b>
    , born on
    <b>{{ $date->format('M, d, Y') }}</b>
    ,
</p>

<p>... wishes to become a member of Study Association Proto.</p>
<p>
    The undersigned is aware of the
    <a
        href="https://wiki.proto.utwente.nl/_media/proto/statues_v3_eng_censor.pdf"
        target="_blank"
    >
        Bylaws
    </a>
    (NL:
    <a
        href="https://wiki.proto.utwente.nl/_media/proto/statuten_v3_nl_censor.pdf"
        target="_blank"
    >
        Statuten)
    </a>
    and
    <a
        href="https://wiki.proto.utwente.nl/_media/proto/rules_and_regulations_s.a._proto_en_.pdf"
        target="_blank"
    >
        Rules & Regulations
    </a>
    (NL:
    <a
        href="https://wiki.proto.utwente.nl/_media/proto/rules_and_regulations_s.a._proto_nl_.pdf"
        target="_blank"
    >
        Huishoudelijk Regelement)
    </a>
    of the association and promises to follow them. (Please note that the Dutch
    version of the documents are leading.)
</p>

<p>
    Membership of the association is renewed annually, following a timely notice
    reminding the member their membership will be renewed. Membership may be
    terminated, without cost, before the start of the new academic year. In
    addition, the undersigned promises to pay for any cost incurred as member of
    the association, including the membership fee, in a timely manner via any of
    the payment options made available by the association.
</p>

<p>
    For the administration of the association, the undersigned provided at the
    time of registration the e-mail address
    <b>{{ $user->email }}</b>
    and phone number
    <b>{{ $user->phone }}</b>
    , as well as the following physical address:

    @php($address = $user->address)
</p>

<ul>
    <li><b>{{ $address->street }} {{ $address->number }}</b></li>
    <li><b>{{ $address->zipcode }} {{ $address->city }}</b></li>
    <li><b>{{ $address->country }}</b></li>
</ul>

<p>
    For the administration of the association, the undersigned promises to make
    sure that during their membership, the association always has a valid e-mail
    address and phone number on which the undersigned can be contacted, as well
    as at least one physical address of the undersigned.
</p>
