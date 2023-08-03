<?php

class TableroController extends Controller
{
	private function titulo($filtro)
	{
		switch ($filtro) {
			case 'registrados': return 'Incidentes registrados';
			case 'abiertos': return 'Incidentes abiertos';
			case 'sin_asignar': return 'Incidentes sin asignar';
			case 'en_progreso': return 'Incidentes en progreso';
			case 'en_pausa': return 'Incidentes en pausa';
			case 'bloqueados': return 'Incidentes bloqueados';
			case 'resueltos': return 'Incidentes resueltos';
			case 'cerrados': return 'Incidentes cerrados';
			case 'cancelados': return 'Incidentes cancelados';
			case 'en_tiempo': return 'Incidentes con SLA en tiempo';
			case 'a_vencer': return 'Incidentes con SLA a vencer';
			case 'vencidos': return 'Incidentes con SLA vencido';
			case 'backlog': return 'Incidentes en backlog';
		}
	}

	public function tablero($filtro = null)
	{
		$this->authorize('tablero_control');

		$filtros = SessionFilters::getFilters('tablero');

		$sla = 3; // Verifica incidentes a vencer faltando 3 horas

		$subQuery = Incidente::selectRaw("id, status, sla, asignado, grupo, cliente, 
			case when horas>0 then horas ELSE (case when pausa>0 then 
			TIMESTAMPDIFF(MINUTE, date_add(fecha_ingreso, INTERVAL FLOOR(pausa*60) MINUTE), NOW()) ELSE 
			TIMESTAMPDIFF(MINUTE, fecha_ingreso, NOW())/60 END) END AS horastotal")
			->whereRaw('(periodo!=0 or periodo is null)');

		$subQuery = IncidentFilter::applyFilters($subQuery, $filtros);
		
		$contador = Incidente::selectRaw("COUNT(*) AS total,
			SUM(case when status=0 then 1 ELSE 0 end) AS sin_asignar,
			SUM(case when status=1 then 1 ELSE 0 end) AS en_progreso,
			SUM(case when status=5 then 1 ELSE 0 end) AS en_pausa,
			SUM(case when status=6 then 1 ELSE 0 end) AS bloqueados,
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

		$contador = $contador->first(); //->toArray();

		$status = '';
		if ($filtro)
		{
			$incidentes = Incidente::with(['inc_cliente', 'inc_usuario', 'inc_asignado'])
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

			$status = $this->titulo($filtro);

			$incidentes = IncidentFilter::applyFilters($incidentes, $tfiltros);

			if ($filtro=="backlog") {
				$incidentes = $incidentes->whereRaw('(periodo=0 and periodo is not null)');
			} else {
				$incidentes = $incidentes->whereRaw('(periodo!=0 or periodo is null)');
			}

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
			$grupos = Auth::user()->rol==1
				? Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get() 
				: Auth::user()->grupos()->orderBy('descripcion')->get();

			$usuarios = Auth::user()->rol==1
				? User::where('tipo', 1)->where('activo', 1)->orderBy('nombre')->get()->toArray()
				: [];

			$clientes = Cliente::where('activo', 1)->orderBy('descripcion')->get();
		}

		$backlog = Incidente::where('periodo', 0)->whereNotNull('periodo');
		
		$backlog = IncidentFilter::applyFilters($backlog, $tfiltros);
		
		$backlog = $backlog->count();

		$query = http_build_query($filtros);

		return view('tablero', compact('incidentes', 'backlog', 'contador', 
			'status', 'usuarios', 'grupos', 'clientes', 'filtros', 'query'));
	}

}