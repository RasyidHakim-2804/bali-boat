<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    public function create($email, $firstName, $lastName = null, $password = null)
    {
        $user = User::where('email', $email)->first();

        if ($user) return $user;

        $user               = new User();
        $user->first_name   = $firstName;
        $user->last_name    = $lastName;
        $user->password     = bcrypt('Y32vsjYJhj12djHG45GhsJJ@776d#$');
        $user->uuid         = $this->generateUuid();
        $user->email        = $email;
        $user->is_active    = 0;

        if (isset($password)) $user->password = bcrypt($password);

        $user->save();

        return $user;
    }

    protected function generateUuid()
    {
        $sameUuid = 1;

        while ($sameUuid > 0) {
            $uuid     = Str::random(15);
            $sameUuid = User::where('uuid', $uuid)->count();
        }
        
        return $uuid;
    }
}