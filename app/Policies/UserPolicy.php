<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SystemAccountResource');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:SystemAccountResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SystemAccountResource');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:SystemAccountResource');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:SystemAccountResource');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SystemAccountResource');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:SystemAccountResource');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:SystemAccountResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SystemAccountResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SystemAccountResource');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:SystemAccountResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SystemAccountResource');
    }
}
