<?php

class IncidentService
{

    public static function calcularPrioridad($cliente, $modulo, $tipo_incidente, $inc)
	{
		$pondera = 0;

		$modulo = Modulo::find($modulo)->pondera;
		$tipoinc = TipoIncidente::find($tipo_incidente)->pondera;
		$tiposerv = Cliente::with('servicio')->find($cliente)->servicio->pondera;

		$pondera += (int)$modulo + (int)$tipoinc + (int)$tiposerv;

		$reglas = Regla::with('condiciones')->where('activo', 1)->get();

		foreach ($reglas as $regla)
		{
			$pondera += self::verificarRegla($regla, $inc);
		}

		return $pondera;
	}

	public static function buscarAsignacion($inc)
	{
        $reglas = Asignacion::with('condiciones')->where('activo', 1)->get();

		foreach ($reglas as $regla)
		{
			$asignacion = self::verificarAsignacion($regla, $inc);

			if ($asignacion)
				return $asignacion;
		}

		return [0, null, 0];
	}

	public static function acentos($cadena) 
	{
		$search = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±,Ã“,Ã ,Ã‰,Ã ,Ãš,â€œ,â€ ,Â¿,ü");
		$replace = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ó,Á,É,Í,Ú,\",\",¿,&uuml;");
		$cadena= str_replace($search, $replace, $cadena);
		$cadena = str_replace("'", "", $cadena);

		return $cadena;
	}

	private static function verificarRegla($regla, $inc)
	{

		foreach ($regla->condiciones as $cond)
		{
			if ($cond->valor == 'dia')
			{
				$dia = date('d', strtotime($inc['fecha_ingreso']));

				if ($cond->operador=='igual')
				{
					if ($dia != $cond->igual) return 0;
				}

				elseif ($cond->operador=='entre')
				{
					if ($dia < $cond->minimo) return 0;
					if ($dia > $cond->maximo) return 0;
				}
			}

			elseif ($cond->valor == 'remitente')
			{
				if ($cond->igual != User::where('Usuario', $inc['usuario'])->first()->idT)
					return 0;
			}

			elseif ($cond->valor == 'tipo_servicio')
			{
				if ($cond->igual != Cliente::where('codigo', $inc['cliente'])->first()->servicio->codigo)
					return 0;
			}

			elseif ($cond->valor == 'cliente')
			{
				if ($cond->igual != $inc['cliente'])
					return 0;
			}

			elseif ($cond->valor == 'area')
			{
				if ($cond->igual != $inc['area'])
					return 0;
			}

			elseif ($cond->valor == 'modulo')
			{
				if ($cond->igual != $inc['modulo'])
					return 0;
			}

			elseif ($cond->valor == 'tipo_incidente')
			{
				if ($cond->igual != $inc['tipo_incidente'])
					return 0;
			}

		}

		return $regla->pondera;
	}

	private static function verificarAsignacion($regla, $inc)
	{
        $found = false;

		foreach ($regla->condiciones as $cond)
		{

			if ($cond->condicion == 'cliente')
            {
                $found = $inc['cliente']==$cond->valor;
            }

            elseif ($cond->condicion == 'area')
            {
                $found = $inc['area']==$cond->valor;
            }

            elseif ($cond->condicion == 'modulo')
            {
                $found = $inc['modulo']==$cond->valor;
            }

            elseif ($cond->condicion == 'tipo_incidente')
            {
                $found = $inc['tipo_incidente']==$cond->valor;
            }

        }

		return $found? [$regla->grupo, $regla->usuario, ($regla->usuario? 1 : 0)] : null;
	}

	public static function calcularHoras($inc)
	{
		//$inc = Incidente::with('avances_resumido')->find($id);
		//echo "--------------------<br>INCIDENTE ".$inc->id."<br>";
		
		if ($inc->sla==0 || $inc->status==50)
		{
			//echo "Incidente cancelado o sin SLA<br>";
			return 0;
		}
		
		$start = strtotime($inc->fecha_ingreso->toDateTimeString());
		$inicio_pausa = 0;
		$pausa_total = 0;
		$horas = 0;
		$status_previo = null;
		
		//echo "INICIO: ".$inc->fecha_ingreso ."<br>";
		
		foreach ($inc->avances->whereIn('tipo_avance', [1, 2, 4, 5, 6, 7, 10, 20, 30]) as $avance)
		{
			//dump($avance);
			# Pausado
			if ($avance->tipo_avance==5)
			{
				//echo "Inicio Pausa: ".$avance->fecha_ingreso ."<br>";
				$inicio_pausa = $avance->fecha_ingreso;
				$horas += strtotime($avance->fecha_ingreso) - $start;
				$start = strtotime($avance->fecha_ingreso);
			}

			# Resuelto
			if ($avance->tipo_avance==10)
			{
				//echo "Resuelto: ".$avance->fecha_ingreso ."<br>";
				$horas += strtotime($avance->fecha_ingreso) - $start;
				
			}

			# Cerrado
			if ($avance->tipo_avance==20 && $status_previo!=10)
			{
				//echo "Cerrado: ".$avance->fecha_ingreso ."<br>";
				$horas += strtotime($avance->fecha_ingreso) - $horas;
				$start = strtotime($avance->fecha_ingreso);
			}

			# Si estaba pausado y sale de esa pausa calculamos las horas
			if ($avance->tipo_avance==7)
			{
				//echo "Fin Pausa: ".$avance->fecha_ingreso ."<br>";
				$pausa_total += strtotime($avance->fecha_ingreso) - strtotime($inicio_pausa);
				//echo "TOTAL Pausa: ".($pausa_total/60/60) ."<br>";
				$start = strtotime($avance->fecha_ingreso);
			}

			# Reapertura
			if ($avance->tipo_avance==6)
			{
				//echo "Reapertura: ".$avance->fecha_ingreso ."<br>";
				$pausa_total += strtotime($avance->fecha_ingreso) - strtotime($inicio_pausa);
				//echo "TOTAL Pausa: ".($pausa_total/60/60) ."<br>";
				$start = strtotime($avance->fecha_ingreso);
			}
			
			$status_previo = $avance->tipo_avance;
			//$temp = $horas/60;
			//echo "HORAS: ". abs(intval($temp/60)) . ":" . abs(intval($temp % 60 )) ."<br>";

		}

		# Si sigue abierto agregamos las horas hasta la fecha actual
		if ($inc->status<10 && $inc->status!=5)
		{
			$horas += strtotime(date("Y-m-d H:i")) - $start;
		}

		$dateDiff = intval($horas / 60);

        //$hrs = abs(intval($dateDiff / 60));
        //$min = abs(intval($dateDiff % 60));
		//echo "TOTAL TIEMPO ABIERTO: $hrs:$min horas -- ". $dateDiff/60 ."<br>";
		return [
			'total' => $dateDiff/60, 
			'pause' => $pausa_total/60/60
		]; 

	}

	public static function aplicarAvance($inc, $tipo_avance, $request)
	{
		if ($tipo_avance==1) # Incidente tomado
		{
			if ($inc->status==0) # Si el estado es 'sin asignar' se cambia al estado 'en proceso'
				$inc->status = 1;

			$inc->grupo =  Auth::user()->grupos->first()->codigo;
			$inc->asignado = Auth::user()->Usuario;
		}

		elseif ($tipo_avance==2) # Incidente derivado
		{
			if ($inc->status==0 && $request->usuario!="null") # Si el estado es 'sin asignar' se cambia al estado 'en proceso'
				$inc->status = 1;

			if ($request->usuario=="null") # Si se asigna a un grupo sin usuario se pasa a 'sin asignar' el estado
				$inc->status = 0;

			$inc->grupo = $request->grupo;
			$inc->asignado = $request->usuario!="null"? $request->usuario : null;
		}

		elseif ($tipo_avance==3) # Incidente bloqueado
			$inc->status = 6;

		elseif ($tipo_avance==5) # A la espera de accion del usuario
			$inc->status = 5;

		elseif ($tipo_avance==6) # Reapertura del incidente
			$inc->status = 1;

		elseif ($tipo_avance==10) # Incidente resuelto
			$inc->status = 10;

		elseif ($tipo_avance==20) # Incidente cerrado
			$inc->status = 20;

		elseif ($tipo_avance==50) # Incidente cancelado
			$inc->status = 50;

		elseif ($tipo_avance==30 && $inc->status==5) # Nota del usuario (quitar pausa)
			$inc->status = 1;

		elseif ($tipo_avance==7) # Incidente retomado (quitar pausa o bloqueo)
			$inc->status = 1;


		if (!$inc->grupo) {
			$inc->grupo =  Auth::user()->grupos->first()->codigo;
		}

		if (!$inc->asignado) {
			$inc->asignado = Auth::user()->Usuario;
		}

		return $inc;
	}

	public static function deshacerAvance($inc, $avance)
	{
		if ($avance->tipo_avance==1 || $avance->tipo_avance==2) {
			$inc->asignado = $avance->asignado_prev? $avance->asignado_prev : null;
			$inc->grupo = $avance->grupo_prev? $avance->grupo_prev : null;
		}

		$inc->status = $avance->status_prev;

		return $inc;
	}


}