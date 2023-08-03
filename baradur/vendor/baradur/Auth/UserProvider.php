<?php

interface UserProvider
{
    public function retrieveById($identifier);
    public function retrieveByToken($identifier, $token);
    public function updateRememberToken($user, $token);
    public function retrieveByCredentials($credentials);
    public function validateCredentials($user, $credentials);
}