<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnswerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Answer $answer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Answer $answer) : bool
    {
        // retourner une response()->json() si l'utilisateur n'est pas autorise
        return $user->id === $answer->user_id;
//            ? Response::allow()
//            : response()->json([
//                'error' => 'Vous n\'etes pas autorise a modifier cette reponse',
//                'status' => 401,
//            ], 401);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Answer $answer) : bool
    {
        return $user->id === $answer->user_id;
//            ? Response::allow()
//            : response()->json([
//                'error' => 'Vous n\'etes pas autorise a supprimer cette reponse',
//                'status' => 401,
//            ], 401);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Answer $answer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Answer $answer): bool
    {
        return false;
    }
}
