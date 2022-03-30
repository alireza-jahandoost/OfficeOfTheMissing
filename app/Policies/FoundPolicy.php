<?php

namespace App\Policies;

use App\Models\Found;
use App\Models\License;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class FoundPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        return !$user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Found $found
     * @return Response|bool
     */
    public function view(User $user, Found $found, License $license)
    {
        return $user->id === $found->user_id && $found->license_id === $license->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return !$user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Found $found
     * @return Response|bool
     */
    public function update(User $user, Found $found, License $license)
    {
        return $user->id === $found->user_id && $found->license_id === $license->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Found $found
     * @return Response|bool
     */
    public function delete(User $user, Found $found, License $license): Response|bool
    {
        return $user->id === $found->user_id && $found->license_id === $license->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Found $found
     * @return Response|bool
     */
    public function restore(User $user, Found $found)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Found $found
     * @return Response|bool
     */
    public function forceDelete(User $user, Found $found)
    {
        //
    }
}
