<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class PhotoAlbumPolicy
{
    public function edit(User $user): Response
    {
        return $user->hasPermissionTo('publishalbums') ?
            Response::allow() :
            Response::deny('You do not have permission to edit this album.');
    }

    public function delete(User $user): Response
    {
        return $user->hasPermissionTo('publishalbums') ?
            Response::allow() :
            Response::deny('You do not have permission to delete this album.');
    }

    public function publish(User $user): Response
    {
        return $user->hasPermissionTo('publishalbums') ?
            Response::allow() :
            Response::deny('You do not have permission to publish this album.');
    }

    public function unpublish(User $user): Response
    {
        return $user->hasPermissionTo('publishalbums') ?
            Response::allow() :
            Response::deny('You do not have permission to unpublish this album.');
    }
}
