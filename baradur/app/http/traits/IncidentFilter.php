<?php

class IncidentFilter {


    public static function applyFilters($data, $filtros)
    {
        if (isset($filtros['buscar']) && $filtros['buscar']!='')
            $data = $data->whereRaw("(incidentes.descripcion LIKE '%".$filtros['buscar']."%' 
                OR incidentes.titulo LIKE '%".$filtros['buscar']."%' 
                OR incidentes.id = '".$filtros['buscar']. "')");

        if (isset($filtros['grupo']))
			$data = $data->where('grupo', $filtros['grupo']);

        if (isset($filtros['usuario']))
            $data = $data->where('asignado', $filtros['usuario']);

        if (isset($filtros['cliente']))
            $data = $data->where('cliente', $filtros['cliente']);

        if (isset($filtros['area']))
            $data = $data->where('area', $filtros['area']);

        if (isset($filtros['tipo_incidente']))
            $data = $data->where('tipo_incidente', $filtros['tipo_incidente']);

        if (isset($filtros['modulo']))
            $data = $data->where('modulo', $filtros['modulo']);

        if (isset($filtros['prioridad']))
            $data = $data->where('pr.pid', $filtros['prioridad']);

        if (isset($filtros['status']))
        {
            if ($filtros['status']=='en_backlog') {
                $data = $data->whereRaw('(periodo=0 and periodo is not null)');
            } 
            else {
                if ($filtros['status']=='abiertos')
                    $data = $data->whereRaw('status < 10');
    
                elseif ($filtros['status']=='finalizados')
                    $data = $data->whereRaw('(status=10 OR status=20)');
    
                elseif ($filtros['status']=='sin_asignar')
                    $data = $data->where('status', 0);
    
                elseif ($filtros['status']=='en_proceso')
                    $data = $data->where('status', 1);
    
                elseif ($filtros['status']=='en_pausa')
                    $data = $data->where('status', 5);
    
                elseif ($filtros['status']=='bloqueados')
                    $data = $data->where('status', 6);
    
                elseif ($filtros['status']=='resueltos')
                    $data = $data->where('status', 10);
    
                elseif ($filtros['status']=='cerrados')
                    $data = $data->where('status', 20);
    
                elseif ($filtros['status']=='cancelados')
                    $data = $data->where('status', 50);

                $data = $data->whereRaw('(periodo!=0 or periodo is null)');
            }
        }

        if (isset($filtros['orden']))
        {
            if ($filtros['orden'] == 'incidentes_asc')
                $data = $data->orderByRaw('id asc');
            
            elseif ($filtros['orden'] == 'incidentes_desc')
                $data = $data->orderByRaw('id desc');

            elseif ($filtros['orden'] == 'prioridad_asc')
                $data = $data->orderByRaw('prioridad asc, id asc');

            elseif ($filtros['orden'] == 'prioridad_desc')
                $data = $data->orderByRaw('prioridad desc, id desc');

            elseif ($filtros['orden'] == 'fecha_asc')
                $data = $data->orderByRaw('fecha_ingreso asc, id asc');

            elseif ($filtros['orden'] == 'fecha_desc')
                $data = $data->orderByRaw('fecha_ingreso desc, id desc');

            elseif ($filtros['orden'] == 'cliente_asc')
                $data = $data->orderByRaw('cliente_desc asc, id asc');

            elseif ($filtros['orden'] == 'cliente_desc')
                $data = $data->orderByRaw('cliente_desc desc, id desc');

            elseif ($filtros['orden'] == 'asignado_asc')
                $data = $data->orderByRaw('asignado asc, id asc');

            elseif ($filtros['orden'] == 'asignado_desc')
                $data = $data->orderByRaw('asignado desc, id desc');

            elseif ($filtros['orden'] == 'estado_asc')
                $data = $data->orderByRaw('status_desc asc, id asc');

            elseif ($filtros['orden'] == 'estado_desc')
                $data = $data->orderByRaw('status_desc desc, id desc');
        }

        if (isset($filtros['tablero']))
        {
            $sla = $filtros['sla'];

            $sqlacase = "case when horas>0 then horas ELSE (case when pausa>0 then 
            TIMESTAMPDIFF(MINUTE, date_add(fecha_ingreso, INTERVAL pausa*60 MINUTE), NOW())/60 ELSE 
            TIMESTAMPDIFF(MINUTE, fecha_ingreso, NOW())/60 END) END";

            
            if ($filtros['tablero']=='abiertos')
            {
                $data = $data->where('status', '<', 10);
            }
            elseif ($filtros['tablero']=='sin_asignar')
            {
                $data = $data->where('status', 0);
            }
            elseif ($filtros['tablero']=='en_progreso')
            {
                $data = $data->whereBetween('status', [1, 9])->where('status', '!=', 5);
            }
            elseif ($filtros['tablero']=='en_pausa')
            {
                $data = $data->where('status', 5);
            }
            elseif ($filtros['tablero']=='bloqueados')
            {
                $data = $data->where('status', 6);
            }
            elseif ($filtros['tablero']=='resueltos')
            {
                $data = $data->where('status', 10);
            }
            elseif ($filtros['tablero']=='cerrados')
            {
                $data = $data->where('status', 20);
            }
            elseif ($filtros['tablero']=='cancelados')
            {
                $data = $data->where('status', 50);
            }
    
            /* elseif ($filtros['tablero']=='en_tiempo')
            {
                $data = $data->whereRaw("( (DATE_ADD(incidentes.fecha_ingreso, INTERVAL sla HOUR) > NOW() 
                    AND DATE_ADD(incidentes.fecha_ingreso, INTERVAL sla-$sla HOUR)>NOW() ) OR sla=0) AND status<10");
            }
            elseif ($filtros['tablero']=='a_vencer')
            {
                $data = $data->whereRaw("DATE_ADD(incidentes.fecha_ingreso, INTERVAL sla HOUR) > NOW() AND 
                    DATE_ADD(incidentes.fecha_ingreso, INTERVAL sla-$sla HOUR) < NOW() AND sla>0 AND status<10");
            }
            elseif ($filtros['tablero']=='vencidos')
            {
                $data = $data->whereRaw('DATE_ADD(incidentes.fecha_ingreso, INTERVAL sla HOUR) < NOW() AND 
                    sla>0 AND status<10');
            } */

            

            elseif ($filtros['tablero']=='en_tiempo')
            {
                $data = $data->whereRaw("((($sqlacase)<sla AND (($sqlacase)+$sla)<sla AND sla>0) OR sla=0) AND status<10");
            }
            elseif ($filtros['tablero']=='a_vencer')
            {
                $data = $data->whereRaw("($sqlacase)<sla AND (($sqlacase)+$sla)>sla AND sla>0 AND status<10");
            }
            elseif ($filtros['tablero']=='vencidos')
            {
                $data = $data->whereRaw("($sqlacase)>=sla AND sla>0 AND status<10");
            }

        }


        if (Auth::user()->rol != 1)
        {
            $rol = Auth::user()->roles;
            $perms = $rol? $rol->permisos->pluck('id')->toArray() : array();
            
            if (in_array(3, $perms)) {
                $data = $data->where('usuario', Auth::user()->Usuario)
                    ->orWhere('remitente', Auth::user()->Usuario)
                    ->orWhere('asignado', Auth::user()->Usuario);
            }
            elseif (in_array(4, $perms) ) {
                if (Auth::user()->tipo==1) {
                    $grupo = Auth::user()->grupos->pluck('codigo')->toArray();
                    if (count($grupo)>0) {
                        $data = $data->whereIn('grupo', Auth::user()->grupos->pluck('codigo')->toArray());
                    }
                } else {
                    $data = $data->where('cliente', Auth::user()->cliente);
                }
            }
        }

        if (Auth::user()->rol != 1)
        {
            $rol = Auth::user()->roles;
            $perms = $rol? $rol->permisos->pluck('id')->toArray() : array();
            
            if (in_array(3, $perms)) {
                $data = $data->where('usuario', Auth::user()->Usuario)
                    ->orWhere('remitente', Auth::user()->Usuario)
                    ->orWhere('asignado', Auth::user()->Usuario);
            }
            elseif (in_array(4, $perms) ) {
                if (Auth::user()->tipo==1) {
                    $grupo = Auth::user()->grupos->pluck('codigo')->toArray();
                    if (count($grupo)>0) {
                        $data = $data->whereIn('grupo', Auth::user()->grupos->pluck('codigo')->toArray());
                    }
                } else {
                    $data = $data->where('cliente', Auth::user()->cliente);
                }
            }
        }

        return $data;

    }

    public static function getUnnasigned()
    {
        $data = Incidente::where('status', 0);

        if (Auth::user()->rol != 1)
        {
            $rol = Auth::user()->roles;
            $perms = $rol? $rol->permisos->pluck('id')->toArray() : array();
            
            if (in_array(3, $perms)) {
                $data = $data->where('asignado', Auth::user()->Usuario);
            }
            elseif (in_array(4, $perms) ) {
                $grupo = Auth::user()->grupos->pluck('codigo')->toArray();
                if (count($grupo)>0) {
                    $data = $data->whereIn('grupo', Auth::user()->grupos->pluck('codigo')->toArray());
                }
            }
        }

        return $data->count();
    }

}