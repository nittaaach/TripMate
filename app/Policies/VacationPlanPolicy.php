<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\VacationPlan;

class VacationPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VacationPlan $plan): bool
    {
        return $user->id === $plan->user_id || $user->role !== UserRole::Customer;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, VacationPlan $plan): bool
    {
        return $user->id === $plan->user_id || $user->role === UserRole::Admin;
    }

    public function delete(User $user, VacationPlan $plan): bool
    {
        return $user->id === $plan->user_id || $user->role === UserRole::Admin;
    }
}
