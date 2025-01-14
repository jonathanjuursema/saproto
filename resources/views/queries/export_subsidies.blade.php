name,primary,email,ut_number,department
@foreach ($users as $user)
    {{ $user->name }},{{ true }},{{ $user->member->UtAccount->mail }},{{ $user->member->UtAccount->number }},
    {{ $user->member->UtAccount->department }}
@endforeach
