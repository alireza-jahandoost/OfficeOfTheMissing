<?php

namespace App\Policies;

use App\Models\License;
use App\Models\Lost;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LostPolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Lost $lost
     * @param License $license
     * @return Response|bool
     */
    public function view(User $user, Lost $lost, License $license): Response|bool
    {
        return $lost->user_id === $user->id && $lost->license_id === $license->id;
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
     * @param Lost $lost
     * @return Response|bool
     */
    public function update(User $user, Lost $lost, License $license): Response|bool
    {
        return $lost->user_id === $user->id && $lost->license_id === $license->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Lost $lost
     * @return Response|bool
     */
    public function delete(User $user, Lost $lost)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Lost $lost
     * @return Response|bool
     */
    public function restore(User $user, Lost $lost)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Lost $lost
     * @return Response|bool
     */
    public function forceDelete(User $user, Lost $lost)
    {
        //
    }
}
