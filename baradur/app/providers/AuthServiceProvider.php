<?php

class AuthServiceProvider extends ServiceProvider
{


    public function boot()
    {
        Gate::define('isadmin', 'AdminPolicy@isAdmin');
        

    }

}
