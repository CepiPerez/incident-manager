<?php

class IncidentesController extends Controller
{
	# Esta function la hice para agregar los tiempos a los
	# incidentes, ya que esas columnas fueron agregadas
	# luego y necesitaba llenarlas con los datos.
	# Ya no sirve, pero la dejo por si en algún momento
	# necesitamos aplicar algun otro cambio
	public function test()
	{
		foreach (Incidente::with('avances')->orderBy('id', 'desc')->get() as $i)
		{
			/* if ($i->status==10 || $i->status==20)
			{ */
				$inc = Incidente::find($i->id);
				$tiempos = IncidentService::calcularHoras($i);
				
				if ($inc->status==10 || $inc->status==20)
				{
					$inc->horas = $tiempos['total'];
					$inc->pausa = $tiempos['pause'];
					echo "INC " . $i->id . " :: HORAS: " . $tiempos['total'] . " :: PAUSA: " . $tiempos['pause'] . "<br>";
					$inc->save();
				}
				elseif ($inc->status==5)
				{
					$inc->horas = $tiempos['total'];
					$inc->pausa = null;
					echo "INC " . $i->id . " :: HORAS: " . $tiempos['total'] . " :: PAUSA: " . $tiempos['pause'] . "<br>";
					$inc->save();
				}
				elseif ($tiempos['pause']!=0)
				{
					$inc->horas = null;
					$inc->pausa = $tiempos['pause'];
					echo "INC " . $i->id . " :: HORAS: " . $tiempos['total'] . " :: PAUSA: " . $tiempos['pause'] . "<br>";
					$inc->save();
				}
				else
				{
					$inc->horas = null;
					$inc->pausa = null;
					$inc->save();
				}

			/* } */
			
		}
		exit();

	}


	public function tareas()
	{
		$periodo = PeriodoController::getCurrentSprint();

		$data = Incidente::selectRaw('incidentes.*, clientes.descripcion as cliente_desc, 
				status_incidentes.descripcion as status_desc, pr.pid, pr.pdesc')
		 	->leftJoin('status_incidentes', 'status_incidentes.codigo', '=', 'incidentes.status')
		 	->leftJoin('clientes', 'clientes.codigo', '=', 'incidentes.cliente')
			->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
				'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT')
			->where('status', '<', 10)
			->where(function ($query) use ($periodo) {
				$query = $query->whereNull('periodo');
				if ($periodo) $query = $query->orWhere('periodo', $periodo->codigo);
				return $query;
			})
			->where('asignado', Auth::user()->Usuario)
			->orderBy('id', 'desc')
			->get();

		//dd($incidentes->toArray());

		//$usuarios = User::activos()->where('tipo', 1)->get()->pluck('nombre', 'Usuario')->toArray();
		//$grupos = Grupo::orderBy('descripcion')->get()->toArray();

		$sin_asignar = IncidentFilter::getUnnasigned();
		$dentro = $periodo ? $data->where('periodo', $periodo->codigo) : $data->whereNotNull('periodo');
		$fuera = $periodo ? $data->where('periodo', '!=', $periodo->codigo) : $data;

		return view('tareas', compact('sin_asignar', 'periodo', 'dentro', 'fuera'));
	}
	
	public function index()
	{
		/* $rol = Auth::user()->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        
		dd($perm);
        foreach ($perm as $p) {
            if ($p > 100) return true;
        } */

		/* dump(Mail::to('cepiperez@gmail.com')
			->subject('Notificacion de prueba')
			->queue(new Notificacion(Incidente::first()))); */

		/* $res = DB::table('baradur_queue')->first();
		dd(unserialize($res->content)); */
			
		$filtros = SessionFilters::getFilters('incidentes');

		$data = Incidente::with('inc_asignado')
			->selectRaw('incidentes.*, clientes.descripcion as cliente_desc, 
				status_incidentes.descripcion as status_desc, pr.pid, pr.pdesc')
		 	->leftJoin('status_incidentes', 'status_incidentes.codigo', '=', 'incidentes.status')
		 	->leftJoin('clientes', 'clientes.codigo', '=', 'incidentes.cliente')
			->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
				'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT');

		$data = IncidentFilter::applyFilters($data, $filtros);

		//dd($data->toPlainSql());

        $data = $data->paginate(20);

		$usuarios = User::activos()->where('tipo', 1)->get()->pluck('nombre', 'Usuario')->toArray();
		$grupos = Grupo::/* with('miembros:Usuario,nombre')-> */orderBy('descripcion')->get()->toArray();

		$sin_asignar = 0;
		$areas = null;
		$tipo_incidente = null;
		$modulos = null;
		$clientes = null;

		if (Auth::user()->tipo==1)
		{
			$sin_asignar = IncidentFilter::getUnnasigned();
			$clientes = Cliente::activos()->get()->toArray();
			$areas = Area::orderBy('descripcion')->get();
			$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
			$modulos = Modulo::orderBy('descripcion')->get();
		}

		return view('incidentes', compact('data', 'clientes', 'usuarios', 'grupos',
			'areas', 'tipo_incidente', 'modulos', 'filtros', 'sin_asignar'));
	}

	public function create()
	{
		$this->authorize('crear_inc');

		$grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$modulos = Modulo::orderBy('descripcion')->get();

		if (Auth::user()->tipo==0) {
			$cliente = Cliente::with('areas')->where('codigo', Auth::user()->cliente)->get()->toArray();
		} else {
			$cliente = Cliente::with(['areas', 'usuarios'])->where('activo', 1)->orderBy('descripcion')->get()->toArray();
		}

		$periodos = Periodo::whereDate('hasta', '>', now())->orderBy('desde')->get();

		return view('incidente-crear', compact('cliente', 'tipo_incidente', 'periodos', 'modulos', 'grupos'));

	}

	public function store(Request $request)
	{
		$request->validate([
			'titulo' => 'required'
		]);

		//dd($request->all()); //exit();

		$inc = [];
		$inc['cliente'] = Auth::user()->tipo==1? $request->cliente : Auth::user()->cliente;
		$inc['area'] = $request->area? $request->area : 0;
		$inc['modulo'] = $request->modulo;
		$inc['programa'] = 0;
		$inc['tipo_incidente'] = $request->tipo_incidente;
		$inc['titulo'] = IncidentService::acentos($request->titulo);
		$inc['descripcion'] = IncidentService::acentos($request->descripcion);
		$inc['menu'] = '';
		$inc['usuario'] = Auth::user()->Usuario;
		$inc['remitente'] = Auth::user()->tipo==1
			? ($request->remitente!="null"? $request->remitente : Auth::user()->Usuario) 
			: Auth::user()->Usuario;
		$inc['punto_menu'] = 0;
		$inc['mail'] = '';
		$inc['periodo'] = null;
		$inc['tel'] = '';
		$inc['status'] = Auth::user()->tipo==1? ($request->asignado!="null"? 1 : 0) : 0;
		$inc['grupo'] = Auth::user()->tipo==1? ($request->grupo!="null"? $request->grupo : 0 ): 0;
		$inc['asignado'] = Auth::user()->tipo==1? ($request->asignado!="null"? $request->asignado : null) : null;
		$inc['fecha_ingreso'] = Auth::user()->tipo==1? Carbon::parse($request->fecha) : now();

		$inc['prioridad'] = IncidentService::calcularPrioridad($request->cliente, $request->modulo, $request->tipo_incidente, $inc);
		$inc['sla'] = TipoIncidente::find($request->tipo_incidente)->sla;
		
		if ($inc['grupo']==0)
		{
			list($auto_group, $auto_user, $auto_status) = IncidentService::buscarAsignacion($inc);
			$inc['grupo'] = $auto_group;
			$inc['asignado'] = $auto_user;
			$inc['status'] = $auto_status;
		}
		
		$res = Incidente::create($inc);

		if (!$res)
		{
			return back()->with('error', 'Hubo un error al guardar el incidente');
		}

		if ($request->hasFile('archivo'))// && $request->file('archivo')->isValid())
		{
			foreach ($request->file('archivo') as $archivo)
			{
				$name = strtolower($archivo->name());
				$path = 'archivos/'.(int)$res->id.'/0';
				$archivo->storeAs($path, $name);
	
				$adjunto = new AdjuntosIncidente;
				$adjunto->incidente = $res->id;
				$adjunto->avance = 0;
				$adjunto->adjunto = $name;
				$adjunto->save();
			}
		}


		// Creamos un avance (sin guardar) para enviar la notificacion
		$avance = new Avance([
			'incidente' => (int)$res->id,
			'tipo_avance' => 101,
			'descripcion' => $inc['descripcion'],
			'destino' => $inc['asignado'],
			'fecha_ingreso' => $inc['fecha_ingreso'],
			'asignado_prev' => 0,
			'grupo_prev' => 0,
			'status_prev' => 0
		]);

		Correo::verificar($avance);

		return to_route('incidentes.index')->with('message', 'Se generó el incidente #'.str_pad($res->id, 7, '0', STR_PAD_LEFT));

	}

	public function show($id)
	{
		$prioridades = Prioridad::selectRaw('id as pid, descripcion as pdesc, minimo, maximo');

		$data = Incidente::with(['inc_cliente', 'tipo_incidente', 'estado', 'avances'])
			->selectRaw('incidentes.*, pr.pid, pr.pdesc, grupos.descripcion as grupo_desc')
			->leftJoin('grupos', 'codigo', '=', 'grupo' )
			->leftJoinSub($prioridades, 'pr', function ($join) {
				$join->on('incidentes.prioridad', '>=', 'pr.minimo');
				$join->on('incidentes.prioridad', '<=', 'pr.maximo');
			})->findOrFail($id);


		$this->authorize('ver_inc', $data);

		if (Auth::user()->tipo==0) {
			$cliente = Cliente::with('areas')->where('codigo', Auth::user()->cliente)->get()->toArray();
		} else {
			$cliente = Cliente::with(['areas', 'usuarios'])->where('activo', 1)->orderBy('descripcion')->get()->toArray();
		}
		
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$usuarios = User::orderBy('nombre')->get(); //->pluck('nombre', 'Usuario')->toArray();
		$modulos = Modulo::orderBy('descripcion')->get();
		$grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();
		

		if ($data->periodo==0 && $data->periodo!==null) {
			$tipo_avance = TipoAvance::whereNotIn('codigo', [3, 5, 6, 7, 10, 20, 50, 101])->get();
		} elseif ($data->status==10) {
			$tipo_avance = TipoAvance::whereIn('codigo', [6, 20])->get();
		} elseif ($data->status==5) {
			$tipo_avance = TipoAvance::where('codigo', 7)->get();
		} elseif ($data->status==6) {
			$tipo_avance = TipoAvance::whereNotIn('codigo', [3, 5, 6, 10, 20, 101])->get();
		} elseif ($data->status==0 || $data->status==1) {
			$tipo_avance = TipoAvance::whereNotIn('codigo', [6, 7, 20, 101])->get();
		} else {
			$tipo_avance = TipoAvance::whereNotIn('codigo', [6, 7, 30, 100, 101])->get();
		}

		$data->tieneEstimacion = false;
		foreach ($data->avances as $avance) {
			if ($avance->tipo_avance==8) {
				$data->tieneEstimacion = true;

				$estimado = unserialize($avance->descripcion);

				$tipo_avance = $tipo_avance->where('codigo', '!=', 8);

				$avance->descripcion = 'Tiempo estimado: ' . implode(' ', $estimado);
			}
		}

		$data->adjunto = AdjuntosIncidente::where('incidente', $id)->where('avance', 0)->get();

		$tiempos = IncidentService::calcularHoras($data);
		$data->horas = $tiempos['total'];


		$rol = Auth::user()->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();

		$periodos = Periodo::whereDate('hasta', '>=', now());
		if (intval($data->periodo)>0) {
			$periodos = $periodos->orWhere('codigo', $data->periodo);
		}
		$periodos = $periodos->orderBy('desde')->get();

		$vencido = false;
		if ($data->periodo!==0 && $data->periodo!==null) {
			$current = $periodos->where('codigo', $data->periodo)->first()->hasta;
			$vencido = Carbon::parse($current)->endOfDay()->timestamp < now()->timestamp;
		}

		return view('incidente-editar', compact('data', /* 'adjuntos', */ 'cliente', 'avances', 
				'modulos', 'tipo_incidente','tipo_avance', 'usuarios', 'grupos', 'periodos', 'vencido'));
	}

	public function update(Request $request, $id)
	{
		//dd($request);

		$request->validate([
			'titulo' => 'required'
		]);

		$inc = Incidente::find($id);

		$newrequest = $request->all();
		$newrequest['fecha_ingreso'] = Auth::user()->tipo==1? Carbon::parse($request->fecha_ingreso) : Carbon::parse($inc->fecha_ingreso);
		$newrequest['titulo'] = IncidentService::acentos($newrequest['titulo']);
		$newrequest['descripcion'] = IncidentService::acentos($newrequest['descripcion']);
		$newrequest['prioridad'] = IncidentService::calcularPrioridad($request->cliente, $request->modulo, $request->tipo_incidente, (array)$inc);
		$newrequest['sla'] = TipoIncidente::find($request->tipo_incidente)->sla;
		$newrequest['periodo'] = $request->periodo=="" ? null : $request->periodo;
		
		$sprintPrevio = $inc->periodo;

		// Primero verificamos que el usuario tenga permisos
		// para mover el incidente a distintos sprints
		if (($sprintPrevio!=$newrequest['periodo']) && (intval($newrequest['periodo']) > 0)) {
			if (Gate::denies('admin_tareas')) {
				return back()->with('error', 'No tiene permisos para asignar el incidente a un sprint');
			}
		}

		$res = $inc->update($newrequest);
		
		if (!$res) {
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}

		// Si hubo un cambio en el sprint del incidente 
		// modificamos el historial de sprints
		if ($sprintPrevio != $inc->periodo) {

			$current = PeriodoController::getCurrentSprint();
			
			// El incidente estaba asignado al sprint en curso
			// debido a que sigue abierto lo eliminamos del historial
			if ($current->codigo == $sprintPrevio) {
				HistorialPeriodo::where('incidente', $inc->id)
					->where('periodo', $sprintPrevio)
					->delete();
			}

			// Agregamos el incidente al historial de sprint nuevo 
			// omitiendo NULL (sin asignar) y 0 (backlog)
			if ($inc->periodo!=null && $inc->periodo!=0) {

				// Eliminamos antes de agregar
				// (esto se agrega para evitar duplicados cuando
				// se cambian las fechas de un sprint ya que
				// un incidente podría quedar desactualizado)
				HistorialPeriodo::where('incidente', $inc->id)
					->where('periodo', $inc->periodo)
					->delete();

				HistorialPeriodo::create([
					'incidente' => $inc->id,
					'periodo' => $inc->periodo,
					'grupo' => $inc->grupo,
					'asignado' => $inc->asignado,
					'status' => $inc->status
				]);
			}
		}

		return back()->with('message', 'Se guardaron los cambios');

	}

	public function guardarAvance(Request $request, $id)
	{
		//dump($request->all()); dd($id); exit();
		//dd(Auth::user()->grupos); exit();

		$inc = Incidente::find($id);

		$tipo_avance = 0;
		if ($request->tipo_avance_sub=='avance')
			$tipo_avance = (int)$request->tipo_avance;
		elseif ($request->tipo_avance_sub=='nota')
			$tipo_avance = Auth::user()->tipo==0? 30 : 100;
		elseif ($request->tipo_avance_sub=='cerrar')
			$tipo_avance = 20;
		elseif ($request->tipo_avance_sub=='cancelar')
			$tipo_avance = 50;
		elseif ($request->tipo_avance_sub=='reabrir')
			$tipo_avance = 6;


		$avance = array();
		$avance['incidente'] = (int)$id;
		$avance['tipo_avance'] = $tipo_avance;
		$avance['descripcion'] =  $tipo_avance==8? serialize([$request->tiempo_estimado, $request->tiempo_estimado_tipo]) : $request->descripcion;
		$avance['usuario'] = Auth::user()->Usuario;
		$avance['fecha_ingreso'] = $tipo_avance==10? Carbon::parse($request->fecha_avance) : date('Y-m-d H:i');
		$avance['asignado_prev'] = $inc->asignado;
		$avance['grupo_prev'] = $inc->grupo;
		$avance['status_prev'] = $inc->status;

		if ($tipo_avance == 2)
		{
			$avance['destino'] = $request->usuario!="null" ? $request->usuario : null;
			$avance['grupo_destino'] = $request->grupo;
		}

		$res = Avance::create($avance);

		if ($res)
		{
			if ($request->hasFile('archivo'))
			{
				foreach ($request->file('archivo') as $archivo)
				{
					$name = strtolower($archivo->name());
					$path = 'archivos/'.(int)$id.'/'.$res->id;
					$archivo->storeAs($path, $name);
		
					$adjunto = new AdjuntosIncidente;
					$adjunto->incidente = $id;
					$adjunto->avance = $res->id;
					$adjunto->adjunto = $name;
					$adjunto->save();
				}
			}

			// Aplicamos el avance al incidente
			$inc = IncidentService::aplicarAvance($inc, $tipo_avance, $request);

			# Guardamos los tiempos del incidente
			$tiempos = IncidentService::calcularHoras($inc);
			
			if ($inc->status==10 || $inc->status==20)
			{
				$inc->horas = $tiempos['total'];
				$inc->pausa = $tiempos['pause'];
			}
			elseif ($tiempos['pause']!=0 && $inc->status!=5)
			{
				$inc->horas = null;
				$inc->pausa = $tiempos['pause'];
			}
			elseif ($inc->status==5)
			{
				$inc->horas = $tiempos['total'];
				$inc->pausa = null;
			}
			else
			{
				$inc->horas = null;
				$inc->pausa = null;
			}
			
			$inc->save();


			// Actualizamos el incidente en el historial de sprints
			// solamente para el sprint en curso
			/* $current = PeriodoController::getCurrentSprint();

			if ($inc->periodo!=null && $inc->periodo!=0 && $current) {
				$sinc = HistorialPeriodo::where('incidente', $inc->id)->where('periodo', $current->codigo)->first();

				if ($sinc) {
					$sinc->grupo = $inc->grupo;
					$sinc->asignado = $inc->asignado;
					$sinc->status = $inc->status;
					$sinc->save();
				}
			} */


			// Enviamos la notificacion por correo
			$envio = Correo::verificar($res);
			
			return back()->with('message', 'Se guardó el avance correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el avance');
		}


	}

	public function descargarAdjunto($incidente, $avance, $archivo)
	{
		Storage::download("archivos/$incidente/$avance/$archivo");
	}

	public function eliminarAvance($id, $avance)
	{
		$av = Avance::where('incidente', (int)$id)->where('id', (int)$avance)->first();

		$actualizarInc = in_array($av->tipo_avance, [1, 2, 3, 5, 6, 7, 10, 20, 50]);

		$inc = $actualizarInc ? Incidente::find((int)$id) : null;

		if ($actualizarInc) {
			$inc = IncidentService::deshacerAvance($inc, $av);
			
			$tiempos = IncidentService::calcularHoras($inc);
	
			if ($inc->status==10 || $inc->status==20){
				$inc->horas = $tiempos['total'];
				$inc->pausa = $tiempos['pause'];
			}
			elseif ($tiempos['pause']!=0 && $inc->status!=5) {
				$inc->horas = null;
				$inc->pausa = $tiempos['pause'];
			}
			else {
				$inc->horas = null;
				$inc->pausa = null;
			}
		}

		if ($av->delete())
		{
			if ($actualizarInc) {
				$inc->save();
	
				// Borramos los adjuntos del avance en la BD
				AdjuntosIncidente::where('incidente', (int)$id)->where('avance', (int)$avance)->delete();
	
				// Borramos los archivos fisicos del avance
				Cache::store('file')->setDirectory(_DIR_.'/storage/app/public/archivos/'.(int)$id.'/'.(int)$avance)->flush();
	
				// Actualizamos el incidente en el historial de sprints
				// solamente para el sprint en curso
				/* if ($inc->periodo!=null && $inc->periodo!=0) {
					$current = PeriodoController::getCurrentSprint();
	
					$sinc = HistorialPeriodo::where('incidente', $inc->id)->where('periodo', $current->codigo)->first();
	
					if ($sinc) {
						$sinc->grupo = $inc->grupo;
						$sinc->asignado = $inc->asignado;
						$sinc->status = $inc->status;
						$sinc->save();
					}
				} */
			}

			return back()->with('message', 'Se eliminó el avance correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el avance');
		}

	}


}
