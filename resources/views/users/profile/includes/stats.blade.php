<div class="card mb-3">
    <div class="card-header px-1 border-bottom-0 text-bg-dark bg-dark">
        <div class="text-center text-bg-dark">
            <h3 class="mb-0">
                User statistics
                <i
                    class="fas fa-info-circle fa-fw mr-2"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="This info is only visible to you!"
                ></i>
            </h3>
            <i>
                <small>
                    Note: These statistics are not visible to other members!
                </small>
            </i>
        </div>
    </div>

    <div class="card-body">
        <p class="card-text ellipsis">
            <i
                class="fas fa-user-clock fa-fw me-2"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="The date your account was created."
            ></i>
            Account created on:
            {{ date('F j, Y', strtotime($user->created_at)) }}.
        </p>

        <p class="card-text ellipsis">
            <i
                class="fas fa-calendar-alt fa-fw me-2"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="The total number of events you have signed up for."
            ></i>
            Total signups: {{ $signups }}
        </p>

        <p class="card-text ellipsis">
            <i
                class="fas fa-box fa-fw me-2"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="The total number of individual products you have purchased."
            ></i>
            Total products purchased: {{ $totalproducts }}
        </p>

        <p class="card-text ellipsis">
            <i
                class="fas fa-euro-sign fa-fw me-2"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="The total amount of money you have spent at Proto."
            ></i>
            Total money spent: €{{ $spentmoney }}
        </p>
    </div>
</div>
