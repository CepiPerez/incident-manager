<?php

class PeriodoController extends Controller
{
	private static function getData($avance, $statusprevio, $userprevio)
	{		
		if ($avance->tipo_avance==1) return [1, $avance->usuario];
		if ($avance->tipo_avance==2 && !$avance->destino) return [0, $userprevio];
		if ($avance->tipo_avance==2 && $avance->destino) return [1, $avance->destino];
		if ($avance->tipo_avance==3) return [6, $userprevio];
		if ($avance->tipo_avance==5) return [5, $userprevio];
		if ($avance->tipo_avance==1) return [1, $userprevio];
		if ($avance->tipo_avance==10) return [10, $userprevio];
		if ($avance->tipo_avance==20) return [20, $userprevio];
		if ($avance->tipo_avance==30) return [10, $userprevio];
		if ($avance->tipo_avance==50) return [50, $userprevio];
		return [$statusprevio, $avance->destino];
	}


	public static function getCurrentSprint()
	{
		$current = null;// Cache::get('sprint');

		if (!$current) {
			$current = Periodo::whereDate('desde', '<=', now())
				->whereDate('hasta', '>=', now())->first();

			if ($current) {
				Cache::forever('sprint', $current);
			}
		}

		return $current;
	}

    public function index()
    {
		$actual = self::getCurrentSprint();

		$this->authorize('periodos');

        $year = request()->a ? Carbon::createFromDate(request()->a, 1, 1) : now();

        $periodos = Periodo::orderBy('desde')
            ->whereYear('desde', $year)
            ->orWhereYear('hasta', $year)
            ->get();

        $timeline = [];
        
        foreach ($periodos as $periodo) {

            $inicio = 1;
            $fin = $year->endOfYear()->format('z') + 1;
            
            if (Carbon::parse($periodo->desde)->year==$year->year) {
                $inicio = Carbon::parse($periodo->desde)->format('z') + 1;
            } 
            
            if (Carbon::parse($periodo->hasta)->year==$year->year) {
                $fin = Carbon::parse($periodo->hasta)->format('z') + 1;
            }

            $timeline[] = [
				'codigo' => $periodo->codigo,
                'nombre' => $periodo->descripcion,
                'inicio' => $inicio,
                'fin' => $fin,
				'dias' => $fin - $inicio,
				'fecha' => Carbon::parse($periodo->desde)->format('d-m-Y') . 
							' al ' . Carbon::parse($periodo->hasta)->format('d-m-Y'),
				'activo' => ($inicio <= now()->format('z')+1) && 
							($fin >= now()->format('z')+1) 
            ];

        }

        $y = $year->year;
        $last = 0;
        $total = $year->endOfYear()->format('z') +1;
        $calendar = [];

        for ($i=1; $i<13; $i++) {
            $day = date('z', mktime(0, 0, 0, $i+1, 1, $y));
            $calendar[$i] = $i==12? 31 : $day - $last;
            $last = $day;
        }
    
		
		if (Gate::any(['admin_tareas', 'inc_backlog'])) {
			$reasignar = Incidente::where(function($q) use($actual) {
					$q = $q->where('periodo', '!=', 0)->whereNotNull('periodo');
					if ($actual) $q = $q->where('periodo', '!=', $actual->codigo);
					return $q;
				})
				->where('status', '<', 10)
				->count();
		} else {
			$reasignar = 0;
		}

		//dd($timeline, $calendar, $year, $total, now()->format('z')+1);

        return view('periodos', compact('periodos', 'calendar', 'timeline', 'reasignar'));
    }

    public function create()
	{
		$this->authorize('crear_periodos');

		return view('periodo-crear');
	}

	public function store(Request $request)
	{
		$desde = Carbon::parse($request->desde);
		$hasta = Carbon::parse($request->hasta);

		if (Periodo::whereDate('desde', '<=', $desde)->whereDate('hasta', '>=', $desde)->exists()) {
			return back()->with('error', 'Ya existe un sprint dentro de las fechas seleccionadas');
		}

		if (Periodo::whereDate('desde', '<=', $hasta)->whereDate('hasta', '>=', $hasta)->exists()) {
			return back()->with('error', 'Ya existe un sprint dentro de las fechas seleccionadas');
		}

		$request->validate([
			'descripcion' => 'required|max:100'
		]);

		$periodo = new Periodo;
		$periodo->descripcion = $request->descripcion;
		$periodo->desde = Carbon::parse($request->desde);
		$periodo->hasta = Carbon::parse($request->hasta);
		$res = $periodo->save();

		if ($res) {
			Cache::forget('sprint');
			self::getCurrentSprint();
			return to_route('periodos.index')->with('message', 'Se guardó el sprint correctamente');
		} else {
			return back()->with('error', 'Hubo un error al guardar el sprint');
		}
	}

	public function show($id)
	{
		$filtro = request()->filtro;

		$this->authorize('periodos');

		if ($id==0) {
			$periodo = new Periodo([
				'codigo' => 0,
				'descripcion' => 'Backlog'
			]);
		} else {
			$periodo = Periodo::findOrFail($id);
		}

		$fin_periodo = Carbon::parse($periodo->hasta);

		if ($id>0) {
			$incidentes = HistorialPeriodo::with([
					'avances' => function($q) use($fin_periodo) {
						return $q->whereDate('fecha_ingreso', '>=', $fin_periodo);
					}, 'avances_estimado'])
				->selectRaw('historial_periodos.incidente, 
					historial_periodos.periodo, pr.pid, pr.pdesc, inc.status, inc.asignado, 
					inc.titulo, clientes.descripcion as cli_desc, inc.fecha_ingreso')
				->leftJoin('incidentes as inc', 'inc.id', '=', 'historial_periodos.incidente')
				->leftJoin('clientes', 'clientes.codigo', '=', 'inc.cliente')
				->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
					'inc.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT')
				->where('periodo', $id)
				->orderBy('incidente', 'desc');
		} else {
			$incidentes = Incidente::with(['avances', 'inc_asignado', 'avances_estimado'])
				->selectRaw('incidentes.*, pr.pid, pr.pdesc, clientes.descripcion as cli_desc,
					status_incidentes.descripcion as status_desc')
				->leftJoin('status_incidentes', 'status_incidentes.codigo', '=', 'incidentes.status')
				->leftJoin('clientes', 'clientes.codigo', '=', 'incidentes.cliente')
				->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
					'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT')
				->where('periodo', $id)
				->orderBy('id', 'desc');
		}
		
		$data = $incidentes->clone()->get();

		$titulo = 'Incidentes asignados';

		if ($filtro && $filtro!='asignados' && $id>0) {
			if ($filtro=='finalizados') {
				$incidentes = $incidentes->where('inc.status', '>=', 10)->where('inc.status', '!=', 50);
			} elseif ($filtro=='pendientes') {
				$incidentes = $incidentes->where('inc.status', '<', 10)->where('inc.status', '!=', 6);
			} elseif ($filtro=='bloqueados') {
				$incidentes = $incidentes->where('inc.status', 6);
			} elseif ($filtro=='cancelados') {
				$incidentes = $incidentes->where('inc.status', 50);
			}

			if ($filtro!='todos') {
				$titulo = 'Incidentes '.$filtro;
			}
		}
		
		$incidentes = $incidentes->paginate(15);

		$usuarios = [];
		$reasignar = 0;

		if ($id>0) {
			$status_incidentes = StatusIncidente::all()->pluck('descripcion', 'codigo')->toArray();

			foreach ($data as $inc) {
				foreach ($inc->avances as $av) {
					$inc = IncidentService::deshacerAvance($inc, $av);
				}
			}

			$arr_users = count($data->pluck('asignado')->toArray()) > 0 
				? User::whereIn('Usuario', $data->pluck('asignado')->toArray())->get()
				: [];

			foreach ($incidentes as $inc) {
				$inc->status = $data->where('incidente', $inc->incidente)->first()->status;
				$inc->status_desc = $status_incidentes[$inc->status];
				$inc->inc_asignado = $arr_users->where('Usuario', $inc->asignado)->first();
			}

			foreach ($arr_users as $user) {
				$usuarios[] = [
					'data' => $user,
					'incidentes' => $data->where('asignado', $user->Usuario)
				];
			}
		
		} 

		/* foreach ($incidentes as $inc) {
			if ($inc->avances_estimado) {
				$inc->avances_estimado->descripcion = Carbon::parse($inc->avances_estimado->descripcion);
			}
		} */

		$total = $data->count();
		$abiertos = $data->where('status', '<', 10)->where('status', '!=', 6)->count();
        $cerrados = $data->where('status', '>=', 10)->where('status', '<', 50)->count();
        $bloqueados = $data->where('status', 6)->count();
        $cancelados = $data->where('status', 50)->count();
		
		$periodos = Periodo::whereDate('hasta', '>=', now())->orderBy('desde')->get();

		return view('periodo-ver', compact('periodo', 'incidentes', 'usuarios', 'periodos',
			'total', 'abiertos', 'cerrados', 'bloqueados', 'cancelados', 'titulo'));
	}

	public function edit($id)
	{
		$this->authorize('periodos');

		if ($id==0) {
			$periodo = new Periodo([
				'codigo' => 0,
				'descripcion' => 'Backlog'
			]);
		} else {
			$periodo = Periodo::findOrFail($id);
		}

		return view('periodo-editar', compact('periodo'));
	}

	public function update(Request $request, $id)
	{
		$periodo = Periodo::findOrFail($id);

		$desde = Carbon::parse($request->desde);
		$hasta = Carbon::parse($request->hasta);
		$adesde = Periodo::whereDate('desde', '<=', $desde)->whereDate('hasta', '>=', $desde)->where('codigo', '!=', $id)->count();
		$ahasta = Periodo::whereDate('desde', '<=', $hasta)->whereDate('hasta', '>=', $hasta)->where('codigo', '!=', $id)->count();

		if ($adesde > 0 || $ahasta > 0) {
			return back()->with('error', 'Ya existe un sprint dentro de las fechas seleccionadas');
		}

		$periodo->descripcion = $request->descripcion;
		$periodo->desde = Carbon::parse($request->desde);
		$periodo->hasta = Carbon::parse($request->hasta);
		$res = $periodo->save();

		if ($res) {
			Cache::forget('sprint');
			self::getCurrentSprint();
			return to_route('periodos.show', $id)->with('message', 'Se guardaron los cambios correctamente');
		} else {
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}
	}

	public function destroy($id)
	{
		$periodo = Periodo::findOrFail($id);
		
		if ($periodo->delete()) {
			DB::statement("UPDATE incidentes set periodo = NULL WHERE periodo=$id");
			DB::statement("DELETE FROM historial_periodos WHERE periodo=$id");
			
			Cache::forget('sprint');
			self::getCurrentSprint();

			return to_route('periodos.index')->with('message', 'Se eliminó el sprint seleccionado');
		} else {
			return back()->with('error', 'Hubo un error al eliminar el sprint');
		}
	}

	public function incidentesVencidos()
	{
		$actual = self::getCurrentSprint();

		$incidentes = Incidente::with('avances', 'inc_asignado')
			->selectRaw('incidentes.*, pr.pid, pr.pdesc, clientes.descripcion as cli_desc,
				status_incidentes.descripcion as status_desc')
			->leftJoin('status_incidentes', 'status_incidentes.codigo', '=', 'incidentes.status')
			->leftJoin('clientes', 'clientes.codigo', '=', 'incidentes.cliente')
			->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
				'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT')
			->where(function($q) use($actual) {
				$q = $q->where('periodo', '!=', 0)->whereNotNull('periodo');
				if ($actual) $q = $q->where('periodo', '!=', $actual->codigo);
				return $q;
			})
			->where('status', '<', 10)
			->orderBy('id', 'desc')
			->paginate(20);

		$periodos = Periodo::whereDate('hasta', '>=', now())->orderBy('desde')->get();
		
		return view('incidentes-vencidos', compact('incidentes', 'periodos'));
	}

	public function moverIncidentes(Request $request)
	{
		$periodo = $request->periodo;
		$incidentes = explode(',', $request->seleccion);

		$res = DB::statement('update incidentes set `periodo`=' . $periodo . ' where id in(' . $request->seleccion . ')');

		if ($res) {
			if ($periodo!==0) {
				foreach ($incidentes as $inc) {
					HistorialPeriodo::create([
						'incidente' => $inc,
						'periodo' => $periodo
					]);
				}
			}

			return to_route('periodos.vencidos')->with('message', 'Se movieron los incidentes seleccionados');
		} else {
			return back()->with('error', 'Hubo un error al mover los incidentes');
		}

	}


}