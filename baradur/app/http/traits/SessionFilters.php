<?php

class SessionFilters
{
    private static $filters = [
        'incidentes' => [
            'grupo', 
            'usuario', 
            'cliente', 
            'status', 
            'area', 
            'tipo_incidente', 
            'modulo', 
            'prioridad', 
            'orden', 
            'buscar', 
            'page'
        ],
        'tablero' => [
            'usuario', 
            'grupo', 
            'cliente'
        ]
    ];


    public static function getFilters($key)
    {
        $defined_filters = self::$filters[$key];

        $request_filters = request()->input();
        $session_filters = $_SESSION[$key] ?? [];
        $coockie_filters = $_COOKIE[$key] ?? [];

        $result = [];

        # Verificamos los filtros a chequear desde el request
        # Si no existen en el request los buscamos en la sesion
        # Si tampoco estan en la sesion los buscamos en las cookies
        foreach ($defined_filters as $filter)
        {
            $current = isset($filter, $request_filters)
                ? $request_filters[$filter]
                : (array_key_exists($filter, $session_filters)
                    ? $session_filters[$filter]
                    : $coockie_filters[$filter]
                );

            # Si buscar está vacío lo dejamos en null
            if ($filter=='buscar') {
                $current = blank($current) ? null : $current;
            }

            # Si la pagina es "1" lo dejamos en null
            elseif ($filter=='page') {
                $current = $current==1 ? null : $current;
            }

            # Para el resto, si es "todos" lo dejamos en null
            elseif ($current=='todos') {
                $current = null;
            }

            # Para el orden, si no existe seteamos por defecto
            if ($filter=='orden' && $current==null) {
                $current = 'incidentes_desc';
            }

            $result[$filter] = $current;
        }

        # Guardamos los filtros en la sesion
		$_SESSION[$key] = $result;

        # Lo guardamos también en las cookies
        setcookie($key, json_encode($result), time()+172800, '/'.env('PUBLIC_FOLDER'), $_SERVER["APP_URL"], false);

        return $result;

    }

}