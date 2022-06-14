<?php

class AdminPolicy
{
    public function isAdmin($user)
    {
        return $user->rol=='admin';

    }

}