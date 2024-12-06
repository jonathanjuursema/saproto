<div id="about" class="card mb-3">

    <div class="card-header text-bg-dark border-bottom-0 bg-dark position-relative mb-2">

        <div>

            <h3 class="text-center ellipsis mb-3 mt-4">
                {{ $user->name }}
            </h3>

            <div class="text-center">

                <img src="{{ $user->generatePhotoPath(170, 170) }}"
                     class="rounded-circle mt-2 border border-5 border-white bg-dark"
                     width="170px" height="170px">

            </div>

        </div>

    </div>

    <div class="card-body">

        @if($user->is_member)
            @if($user->member->membership_type === \App\Enums\MembershipTypeEnum::HONORARY)
                <p class="card-text ellipsis">
                    <i class="fas fa-trophy fa-fw me-3 text-primary" aria-hidden="true"></i>
                    <strong>{{ $user->calling_name }} is an honorary member.</strong>
                </p>
            @elseif($user->member->membership_type === \App\Enums\MembershipTypeEnum::PET)
                <p class="card-text ellipsis">
                    <i class="fas fa-paw fa-fw me-3 text-primary" aria-hidden="true"></i>
                    <strong>{{ $user->calling_name }} is a pet.</strong>
                </p>
            @endif
        @endif

        @can('board')
            <p class="card-text ellipsis">
                <i class="fas fa-user-cog fa-fw me-3 text-info" aria-hidden="true"></i>
                <a href="{{ route('user::admin::details', ['id'=>$user->id]) }}">
                    View this user in the user administration.
                </a>
            </p>
        @endcan

        <p class="card-text ellipsis">
            <i class="fas fa-envelope fa-fw me-3"></i>
            <a href="mailto:{{ $user->getDisplayEmail() }}">{{ $user->getDisplayEmail() }}</a>
        </p>

        @if($user->website)
            <p class="card-text ellipsis">
                <i class="fas fa-globe-africa fa-fw me-3"></i>
                <a href="{{ $user->websiteUrl() }}">{{ $user->websiteDisplay() }}</a>
            </p>
        @endif

        @if($user->phone_visible)
            <p class="card-text ellipsis">
                <i class="fas fa-phone fa-fw me-3" aria-hidden="true"></i>
                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
            </p>
        @endif

        @if($user->address_visible && $user->address != null)
            <p class="card-text ellipsis">
                <i class="fas fa-home fa-fw me-3" aria-hidden="true"></i>
                {{ $user->address->street }} {{ $user->address->number }}, {{ $user->address->city }}
            </p>
        @endif

        <p class="card-text ellipsis">

            @if(!$user->is_member)
                <i class="fas fa-user-times fa-fw me-3"></i>
                Not a member
            @else
                <i class="fas fa-user-friends fa-fw me-3"></i>
                Member
                @if(date('U', strtotime($user->member->created_at)) > 0)
                    as of {{ date('F j, Y', strtotime($user->member->created_at)) }}.
                @else
                    since <strong>before we kept track</strong>!
                @endif
            @endif

        </p>

    </div>

</div>
