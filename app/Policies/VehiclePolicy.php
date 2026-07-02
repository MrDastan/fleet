<?php

namespace App\Policies;

use App\Models\User;

class VehiclePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'fleet']);
    }
}
