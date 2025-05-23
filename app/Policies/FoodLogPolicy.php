<?php
namespace App\Policies;

use App\Models\FoodLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FoodLogPolicy
{
    use HandlesAuthorization;

    public function update(User $user, FoodLog $foodLog)
    {
        return $user->id === $foodLog->user_id;
    }

    public function delete(User $user, FoodLog $foodLog)
    {
        return $user->id === $foodLog->user_id;
    }
}