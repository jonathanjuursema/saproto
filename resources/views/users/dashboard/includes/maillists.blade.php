<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        E-mail preferences
    </div>
    <div class="card-body">

        <p class="card-text">
            We offer a number of e-mail lists you can subscribe to to receive information related to you. We
            chose this approach so that you can finely tune what information is relevant for you. You can always
            unsubscribe from an e-mail list below, or by following the link at the bottom of an e-mail. Please note that
            you cannot unsubscribe for some e-mails. Click on the list for more info.
        </p>

        @if(EmailList::all()->count() > 0)

            <div class="accordion" id="email__accordion">

                @foreach(EmailList::all() as $i => $list)

                    <div class="card border">
                        <div class="card-header border-bottom-0" data-toggle="collapse"
                             data-target="#email__collapse__{{ $list->id }}">
                            {{ $list->name }}

                            @if($list->isSubscribed($user))
                                <a href="{{ route('togglelist', ['id'=>$list->id]) }}"
                                   class="badge badge-danger float-right">Unsubscribe</a>
                            @elseif(!$list->is_member_only || $user->member)
                                <a href="{{ route('togglelist', ['id'=>$list->id]) }}"
                                   class="badge badge-info float-right">Subscribe</a>
                            @else
                                <span class="badge badge-dark float-right">Members only</span>
                            @endif
                        </div>

                        <div id="email__collapse__{{ $list->id }}" class="collapse" data-parent="#email__accordion">
                            <div class="card-body">
                                {!! Markdown::convertToHtml($list->description) !!}
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>

        @endif

    </div>
</div>