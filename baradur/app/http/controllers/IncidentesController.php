<?php

class IncidentesController extends Controller
{

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

	
	public function index()
	{

		/* $test = 10;
		dd(Incidente::withCount(['avances as resoluciones' => function ($query) {
			$query->where('tipo_avance', 10);
		}, 'avances' => function ($query) use($test) {
			$query->where('tipo_avance', $test);
		}])->take(2)->get()); */

		/* dump(Mail::to('cepiperez@gmail.com')
			->subject('Notificacion de prueba')
			->queue(new Notificacion(Incidente::first()))); */

		/* $res = DB::table('baradur_queue')->first();
		dd(unserialize($res->content)); */
	
		$filtros = SessionFilters::getFilters(request(), 'filtros', 
			['grupo', 'usuario', 'cliente', 'status', 'tipo_incidente', 'modulo', 'prioridad', 'orden', 'buscar', 'p']);

		//setcookie(env('APP_NAME').'_filters', json_encode($filtros), time()+172800, '/'.env('PUBLIC_FOLDER'), $_SERVER["APP_URL"], false);

		$data = Incidente::with('inc_asignado')
			->selectRaw('incidentes.*, clientes.descripcion as cliente_desc, 
				status_incidentes.descripcion as status_desc, pr.pid, pr.pdesc')
		 	->leftJoin('status_incidentes', 'codigo', '=', 'status')
		 	->leftJoin('clientes', 'codigo', '=', 'cliente')
			->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
				'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT');

		
		$data = IncidentFilter::applyFilters($data, $filtros);

        $data = $data->paginate(20);

		$usuarios = User::activos()->where('tipo', 1)->get()->pluck('nombre', 'Usuario')->toArray();
		$grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();

		$sin_asignar = 0;
		$tipo_incidente = null;
		$modulos = null;
		$clientes = null;

		if (Auth::user()->tipo==1)
		{
			$sin_asignar = count($data->where('status', 0));
			$clientes = Cliente::activos()->get()->toArray();
			$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
			$modulos = Modulo::orderBy('descripcion')->get();
		}

		//exit();
		return view('incidentes', compact('data', 'clientes', 'usuarios', 'grupos',
			'tipo_incidente', 'modulos', 'filtros', 'sin_asignar'));
	}

	public function create()
	{
		//$usuarios = User::activos()->where('tipo', 1)->pluck('nombre', 'Usuario')->toArray();
		$grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$modulos = Modulo::orderBy('descripcion')->get();
		//$status = StatusIncidente::whereBetween('codigo', [1,19])->get();

		if (Auth::user()->tipo==0)
		{
			$cliente = Cliente::with('areas')->where('codigo', Auth::user()->cliente)->get()->toArray();
		}
		else
		{
			$cliente = Cliente::with(['areas', 'usuarios'])->where('activo', 1)->orderBy('descripcion')->get()->toArray();
		}
		//dd($grupos); exit();
		//header('Content-type: text/plain; charset=utf-8');

		return view('incidente-crear', compact('cliente', 'tipo_incidente', /* 'status', */ 'modulos', 'grupos'));

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
		$inc['remitente'] = Auth::user()->tipo==1?
			($request->remitente!="null"? $request->remitente : Auth::user()->Usuario) : Auth::user()->Usuario;
		$inc['punto_menu'] = 0;
		$inc['mail'] = '';
		$inc['tel'] = '';
		$inc['status'] = Auth::user()->tipo==1? ($request->asignado!="null"? 1 : 0) : 0;
		$inc['grupo'] = Auth::user()->tipo==1? ($request->grupo!="null"? $request->grupo : 0 ): 0;
		$inc['asignado'] = Auth::user()->tipo==1? ($request->asignado!="null"? $request->asignado : null) : null;
		$inc['fecha_ingreso'] = Auth::user()->tipo==1? Carbon::parse($request->fecha) : now();
		
		$inc['prioridad'] = IncidentService::calcularPrioridad($request->cliente, $request->modulo, $request->tipo_incidente, $inc);
		$inc['sla'] = TipoIncidente::find($request->tipo_incidente)->sla;
		
		//dd($inc); exit();

		if ($inc['grupo']==0)
		{
			list($auto_group, $auto_user, $auto_status) = IncidentService::buscarAsignacion($inc);
			$inc['grupo'] = $auto_group;
			$inc['asignado'] = $auto_user;
			$inc['status'] = $auto_status;
		}
		
		
		//dd($inc); exit();


		$res = Incidente::create($inc);

		if (!$res)
		{
			return back()->with('error', 'Hubo un error al guardar el incidente');
		}
		else
		{
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

			return to_route('incidentes.index')->with('message', 'Se generó el incidente correctamente');
		}
	}

	public function show($id)
	{
		$data = Incidente::with(['inc_cliente', 'tipo_incidente', 'estado', 'avances'])
			->selectRaw('incidentes.*, pr.pid, pr.pdesc, grupos.descripcion as grupo_desc')
			->leftJoin('grupos', 'codigo', '=', 'grupo' )
			->joinSub('SELECT id as pid, descripcion as pdesc, minimo, maximo from prioridades', 'pr',
			'incidentes.prioridad', 'BETWEEN', 'minimo AND maximo', 'LEFT')
			->findOrFail($id);

		$this->authorize('ver_inc', $data);

		if (Auth::user()->tipo==0)
		{
			$cliente = Cliente::with('areas')->where('codigo', Auth::user()->cliente)->get()->toArray();
		}
		else
		{
			$cliente = Cliente::with(['areas', 'usuarios'])->where('activo', 1)->orderBy('descripcion')->get()->toArray();
		}
		
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$usuarios = User::orderBy('nombre')->get(); //->pluck('nombre', 'Usuario')->toArray();
		$modulos = Modulo::orderBy('descripcion')->get();
		$grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();
		
		$tipo_avance = $data->status->codigo==10? 
			TipoAvance::whereIn('codigo', [6, 20])->get() :
			(
				$tipo_avance = $data->status->codigo==5? 
					TipoAvance::where('codigo', 7)->get():
					TipoAvance::whereNotIn('codigo', [6, 7, 30, 100, 101])->get()
			);


		$data->adjunto = AdjuntosIncidente::where('incidente', $id)->where('avance', 0)->get();

		$tiempos = IncidentService::calcularHoras($data);
		$data->horas = $tiempos['total'];

		//dd(Utils::sla_expiration($data->horas, $data->sla));
		//dd($data->avances); exit();

		return view('incidente-editar', compact('data', /* 'adjuntos', */ 'cliente', 'avances', 
				'modulos', 'tipo_incidente','tipo_avance', 'usuarios', 'grupos'));
	}

	public function update(Request $request, $id)
	{
		//dd($request->all()); exit();

		$request->validate([
			'titulo' => 'required'
		]);

		$inc = Incidente::find($id);

		$newrequest = $request->all();
		$newrequest['fecha_ingreso'] = Auth::user()->tipo==1? Carbon::parse($request->fecha) : Carbon::parse($inc->fecha_ingreso);
		$newrequest['titulo'] = IncidentService::acentos($newrequest['titulo']);
		$newrequest['descripcion'] = IncidentService::acentos($newrequest['descripcion']);

		$newrequest['prioridad'] = IncidentService::calcularPrioridad($request->cliente, $request->modulo, $request->tipo_incidente, (array)$inc);
		$newrequest['sla'] = TipoIncidente::find($request->tipo_incidente)->sla;

		$res = $inc->update($newrequest);

		if (!$res)
			return back()->with('error', 'Hubo un error al guardar los cambios');
		else
			return back()->with('message', 'Se guardaron los cambios');

	}

	public function guardarAvance(Request $request, $id)
	{
		//dd($request->all()); dd($id); exit();
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
		$avance['descripcion'] = $request->descripcion;
		$avance['usuario'] = Auth::user()->Usuario;
		$avance['fecha_ingreso'] = date('Y-m-d H:i');
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

				$inc->grupo = $request->grupo;
				$inc->asignado = $request->usuario!="null"? $request->usuario : null;
			}
				
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

			elseif ($tipo_avance==7) # Incidente retomado (quitar pausa)
				$inc->status = 1;

			# Si se resuelve guardamos la fecha
			# Si se cierra sin resolver usamos esa fecha
			//if ($tipo_avance==10 || ($tipo_avance==20 && $inc->status!=10))
			//	$inc->fecha_resolucion = date('Y-m-d H:i');

			//dump($inc);

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

			//dd($inc);
			$inc->save();

			//$envio = Correo::verificar($res);
			

			return back()->with('message', 'Se guardó el avance correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el avance');
		}


	}

	/* public function guardarNota(Request $request, $id)
	{
		//dd($request);
		$avance = array();
		$avance['incidente'] = (int)$id;
		$avance['tipo_avance'] = Auth::user()->tipo==0? 30 : 100;
		$avance['descripcion'] = $request->descripcion_nota;
		$avance['usuario'] = Auth::user()->Usuario;
		$avance['fecha_ingreso'] = date('Y-m-d H:i');
		
		$res = Avance::create($avance);

		if ($res)
		{
			if ($request->hasFile('archivonota'))
			{
				if ($request->file('archivonota')->isValid())
				{
					$name = strtolower($request->file('archivonota')->name());
					$path = 'archivos/'.(int)$id.'/'.$res->id;
					$request->file('archivonota')->storeAs($path, $name);
		
					$adjunto = new AdjuntosIncidente;
					$adjunto->incidente = $id;
					$adjunto->avance = $res->id;
					$adjunto->adjunto = $name;
					$adjunto->save();
				}
			}

			//$inc = Incidente::find($id);

			return back()->with('message', 'Se guardó la correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar la nota');
		}


	} */

	public function descargarAdjunto($incidente, $avance, $archivo)
	{
		Storage::download("archivos/$incidente/$avance/$archivo");
	}

	public function eliminarAvance($id, $avance)
	{
		$av = Avance::where('incidente', (int)$id)->where('id', (int)$avance)->first();

		$inc = Incidente::find((int)$id);

		if ($av->tipo_avance==1 || $av->tipo_avance==2)
		{
			$inc->asignado = $av->asignado_prev? $av->asignado_prev : null;
			$inc->grupo = $av->grupo_prev? $av->grupo_prev : null;
		}

		if ($av->status_prev)
			$inc->status = $av->status_prev;

		
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
		else
		{
			$inc->horas = null;
			$inc->pausa = null;
		}
				
		if ($av->delete())
		{
			//ddd($inc->_getOriginalKeys());
			$inc->save();
			AdjuntosIncidente::where('incidente', (int)$id)->where('avance', (int)$avance)->delete();
			Cache::store('file')->setDirectory(_DIR_.'/storage/app/public/archivos/'.(int)$id.'/'.(int)$avance)->flush();
			return back()->with('message', 'Se eliminó el avance correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el avance');
		}

	}


}
