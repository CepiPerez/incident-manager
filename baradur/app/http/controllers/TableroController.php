<?php

class TableroController extends Controller
{

	public function tablero($filtro = null)
	{

		$this->authorize('tablero_control');

		$filtros = SessionFilters::getFilters(request(), 'tablero', ['usuario', 'grupo', 'cliente']);

		$sla = 3; // Verifica incidentes a vencer faltando 3 horas

		/* $contador = Incidente::selectRaw("COUNT(*) AS total,
			SUM(case when status=0 then 1 ELSE 0 end) AS sin_asignar,
			SUM(case when (status BETWEEN 1 AND 9) AND (status!=5)  then 1 ELSE 0 end) AS en_progreso,
			SUM(case when status=5 then 1 ELSE 0 end) AS en_pausa,
			SUM(case when status=10 then 1 ELSE 0 end) AS resueltos,
			SUM(case when status=20 then 1 ELSE 0 end) AS cerrados,
			SUM(case when status=50 then 1 ELSE 0 end) AS cancelados,
            SUM(case when status<10 then 1 ELSE 0 end) AS abiertos,
			SUM(case when sla=0 then 1 ELSE 0 end) AS sin_sla,
			SUM(case when sla>0 then 1 ELSE 0 end) AS con_sla,
            SUM(case when ((DATE_SUB(DATE_ADD(fecha_ingreso, INTERVAL sla HOUR), INTERVAL pausa HOUR)>NOW() AND DATE_ADD(fecha_ingreso, INTERVAL sla - $sla HOUR)>NOW()) OR sla=0) AND status<10 then 1 ELSE 0 end) AS en_tiempo,
            SUM(case when DATE_SUB(DATE_ADD(fecha_ingreso, INTERVAL sla HOUR), INTERVAL pausa HOUR)>NOW() AND DATE_ADD(fecha_ingreso, INTERVAL sla - $sla HOUR)<NOW() AND sla>0 AND status<10 then 1 ELSE 0 end) AS a_vencer,
            SUM(case when DATE_SUB(DATE_ADD(fecha_ingreso, INTERVAL sla HOUR), INTERVAL pausa HOUR)<NOW() AND sla>0 AND status<10 then 1 ELSE 0 end) AS vencidos");
		 */


		/* $sqlacase = "case when horas>0 then horas ELSE (case when pausa>0 then 
		TIMESTAMPDIFF(MINUTE, date_add(fecha_ingreso, INTERVAL pausa*60 MINUTE), NOW())/60 ELSE 
		TIMESTAMPDIFF(MINUTE, fecha_ingreso, NOW())/60 END) END";


		$contador = Incidente::selectRaw("
			COUNT(*) AS total,
			SUM(case when status=0 then 1 ELSE 0 end) AS sin_asignar,
			SUM(case when (status BETWEEN 1 AND 9) AND (status!=5)  then 1 ELSE 0 end) AS en_progreso,
			SUM(case when status=5 then 1 ELSE 0 end) AS en_pausa,
			SUM(case when status=10 then 1 ELSE 0 end) AS resueltos,
			SUM(case when status=20 then 1 ELSE 0 end) AS cerrados,
			SUM(case when status=50 then 1 ELSE 0 end) AS cancelados,
            SUM(case when status<10 then 1 ELSE 0 end) AS abiertos,
			SUM(case when sla=0 then 1 ELSE 0 end) AS sin_sla,
			SUM(case when sla>0 then 1 ELSE 0 end) AS con_sla,
            SUM(
				case when ((($sqlacase)<sla AND (($sqlacase)+$sla)<sla AND sla>0) OR sla=0) AND status<10 then 1 ELSE 0 END
			) AS en_tiempo,
            SUM(
				case when ($sqlacase)<sla AND (($sqlacase)+$sla)>sla AND sla>0 AND status<10 then 1 ELSE 0 END
			) AS a_vencer,
            SUM(
				case when ($sqlacase)>sla AND sla>0 AND status<10 then 1 ELSE 0 END
			) AS vencidos
		"); */

		//$contador = IncidentFilter::applyFilters($contador, $filtros);

		
		$subQuery = Incidente::selectRaw("id, status, sla, asignado, grupo, cliente, 
			case when horas>0 then horas ELSE (case when pausa>0 then 
			TIMESTAMPDIFF(MINUTE, date_add(fecha_ingreso, INTERVAL FLOOR(pausa*60) MINUTE), NOW()) ELSE 
			TIMESTAMPDIFF(MINUTE, fecha_ingreso, NOW())/60 END) END AS horastotal");

		$subQuery = IncidentFilter::applyFilters($subQuery, $filtros);
		
		$contador = Incidente::selectRaw("COUNT(*) AS total,
			SUM(case when status=0 then 1 ELSE 0 end) AS sin_asignar,
			SUM(case when (status BETWEEN 1 AND 9) AND (status!=5)  then 1 ELSE 0 end) AS en_progreso,
			SUM(case when status=5 then 1 ELSE 0 end) AS en_pausa,
			SUM(case when status=10 then 1 ELSE 0 end) AS resueltos,
			SUM(case when status=20 then 1 ELSE 0 end) AS cerrados,
			SUM(case when status=50 then 1 ELSE 0 end) AS cancelados,
            SUM(case when status<10 then 1 ELSE 0 end) AS abiertos,
			SUM(case when sla=0 then 1 ELSE 0 end) AS sin_sla,
			SUM(case when sla>0 then 1 ELSE 0 end) AS con_sla,
			SUM(case when ((horastotal<sla AND (horastotal+$sla)<sla AND sla>0) OR sla=0) AND status<10 then 1 ELSE 0 END) AS en_tiempo,
			SUM(case when horastotal<sla AND (horastotal+$sla)>sla AND sla>0 AND status<10 then 1 ELSE 0 END) AS a_vencer,
			SUM(case when horastotal>=sla AND sla>0 AND status<10 then 1 ELSE 0 END) AS vencidos");

		$contador = $contador->fromSub($subQuery, 'inc');

		$contador = $contador->get()->first();

		$status = '';
		if ($filtro)
		{
			$incidentes = Incidente::with('cliente', 'inc_usuario', 'inc_asignado')
				->selectRaw('incidentes.*, pr.pid, pr.pdesc')
				->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
				'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT');
			

			$tfiltros = array(
				'tablero' => $filtro,
				'usuario' => $filtros['usuario']? $filtros['usuario'] : null,
				'grupo' => $filtros['grupo']? $filtros['grupo'] : null,
				'cliente' => $filtros['cliente']? $filtros['cliente'] : null,
				'sla' => $sla
			);

			$incidentes = IncidentFilter::applyFilters($incidentes, $tfiltros);


			if ($filtro=='registrados')
				$status = 'Incidentes registrados';

			elseif ($filtro=='abiertos')
				$status = 'Incidentes abiertos';

			elseif ($filtro=='sin_asignar')
				$status = 'Incidentes sin asignar';

			elseif ($filtro=='en_progreso')
				$status = 'Incidentes en progreso';

			elseif ($filtro=='en_pausa')
				$status = 'Incidentes en pausa';

			elseif ($filtro=='resueltos')
				$status = 'Incidentes resueltos';

			elseif ($filtro=='cerrados')
				$status = 'Incidentes cerrados';

			elseif ($filtro=='cancelados')
				$status = 'Incidentes cancelados';

			elseif ($filtro=='en_tiempo')
				$status = 'Incidentes con SLA en tiempo';

			elseif ($filtro=='a_vencer')
				$status = 'Incidentes con SLA a vencer';

			elseif ($filtro=='vencidos')
				$status = 'Incidentes con SLA vencido';


			$incidentes = $incidentes->orderBy('id', 'desc')->paginate(15);

		}
		else
		{
			$incidentes = null;
		}

		$grupos = [];
		$usuarios = [];

		if (Auth::user()->tipo==1)
		{
			$grupos = Auth::user()->rol==1? Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get() : 
				Auth::user()->grupos()->orderBy('descripcion')->get();

			$usuarios = Auth::user()->rol==1? User::where('tipo', 1)->orderBy('nombre')->get() : [];

			$clientes = Cliente::where('activo', 1)->orderBy('descripcion')->get();

		}

		return view('tablero', compact('incidentes', 'contador', 'status', 'usuarios', 'grupos', 'clientes', 'filtros'));
	}

	/* public function tableroFiltrado($param)
	{
		return $this->tablero($param);
	} */


}