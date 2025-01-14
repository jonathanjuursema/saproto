@include(
    'users.includes.usercard',
    [
        'user' => $membership->user,
        'subtitle' => sprintf(
            '<em>%s</em><br>%s',
            $membership->role ? $membership->role : 'General Member',
            $membership->deleted_at
                ? sprintf(
                    'Between %s and %s',
                    date('M \'y', strtotime($membership->created_at)),
                    date('M \'y', strtotime($membership->deleted_at)),
                )
                : sprintf(
                    'Since %s',
                    date('j F Y', strtotime($membership->created_at)),
                ),
        ),
        'footer' =>
            Route::current()->getName() == 'committee::edit'
                ? sprintf(
                    '<a class="btn btn-primary float-end" href="%s"><i class="fas fa-pencil-alt fa-fw"></i></a>',
                    route('committee::membership::edit', [
                        'id' => $membership->id,
                    ]),
                )
                : null,
    ]
)
