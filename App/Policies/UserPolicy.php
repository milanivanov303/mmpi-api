<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends \Core\Policies\Policy
{
    /**
     * Determine if user can read users.
     *
     * @param  User  $user
     * @return bool
     */
    public function read(User $user)
    {
        /**
         * Add logic here
         */
        return true;
    }
}
