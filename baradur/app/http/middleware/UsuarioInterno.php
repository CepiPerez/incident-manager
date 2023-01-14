<?php

class UsuarioInterno extends Middleware
{

    public function handle($request, $next)
    {
        if (Auth::user()->tipo==0)
            abort(403);

        return $next($request);
        
    }

}
