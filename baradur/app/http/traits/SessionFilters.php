<?php

class SessionFilters
{

    public static function getFilters($request, $session_key, $filters)
    {
        $filtros = array();
        $check = array();

        # Verificamos los filtros a chequear desde el request
        foreach ($filters as $filter)
        {
            $check[$filter] = $request->$filter;
        }

        # Si no hay una sesion con los filtros, buscamos si existe una cookie
        # Si es el caso, guardamos los filtros en la sesion
		if (!isset($_SESSION[$session_key]))
		{
			$t = env('APP_NAME').'_filters';
			if (isset($_COOKIE[$t]))
				$_SESSION[$session_key] = json_decode($_COOKIE[$t], true);
		}

        # Seteamos los filtros desde la sesion
		if (isset($_SESSION[$session_key]))
		{
            foreach ($filters as $filter)
            {
                if ($filter=='buscar')
                {
                    if (isset($_SESSION[$session_key]['buscar']) && is_null($check[$filter])) 
                    $check[$filter] = $_SESSION[$session_key][$filter];
                    
                }
                else
                {
                    if (isset($_SESSION[$session_key][$filter]) && !$check[$filter])
                        $check[$filter] = $_SESSION[$session_key][$filter];
                }
            }
		} 

        # Tomamos los filtros que no devuelvan 'todos'
        foreach ($filters as $filter)
        {
            if ($filter=='buscar' && $check[$filter]!='')
                $filtros[$filter] = $check[$filter];

            if ($filter=='orden' && $check[$filter]!='')
                $filtros[$filter] = $check[$filter];

            if ($filter=='p' && $check[$filter]!=1)
                $filtros[$filter] = $check[$filter];

            elseif ($check[$filter] && $check[$filter]!='todos')
                $filtros[$filter] = $check[$filter];
        }

        # Guardamos los filtros finales (excluyendo los que no aplican)
		$_SESSION[$session_key] = $filtros;

        setcookie(env('APP_NAME').'_filters', json_encode($filtros), time()+172800, '/'.env('PUBLIC_FOLDER'), $_SERVER["APP_URL"], false);

        return $filtros;

    }





}