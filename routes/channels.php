<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
     * Authenticate the user's personal channel...
 */
Broadcast::channel('App.User.*', fn ($user, $userId): bool => (int) $user->id === (int) $userId);

Broadcast::channel('wallstreet-prices.{wallstreetId}', fn ($user, $wallstreetId) => Auth::user()->can('tipcie'));
