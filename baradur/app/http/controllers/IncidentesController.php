<?php

class IncidentesController extends Controller
{
	protected $clientes = [
		'MECON' => 1,
		'SENASA' => 2,
		'SSN' => 3,
		'PSA' => 4,
		'NewRol' => 5,
		'ANAC' => 6,
		'MINAGRO' => 7,
		'MINEM' => 8,
		'INASE' => 9,
		'T-Fiscal' => 10,
		'La Pampa' => 11,
		'SSS' => 12,
		'EANA' => 13,
		'JIACC' => 14,
		'MINCOM' => 15,
		'PLUSMAR' => 16,
		'PROD' => 17,
		'MINDEF' => 18,
		'MINTUR' => 19,
		'AVELLANEDA' => 20,
		'DISCA' => 21,
		'INPI' => 22,
		'SEGEMAR' => 23,
		'IGN' => 24
	];


	private function calcularPrioridad($cliente, $modulo, $tipo_incidente)
	{
		$pondera = 0;

		$modulo = Modulo::find($modulo)->pondera;
		$tipoinc = TipoIncidente::find($tipo_incidente)->pondera;
		$tiposerv = Cliente::with('servicio')->find($cliente)->servicio->pondera;

		$pondera += (int)$modulo + (int)$tipoinc + (int)$tiposerv;

		$dia = date('d');
		
		if ($dia < 11)
		{
			$pondera += 10;
		}
		elseif ($dia < 21)
		{
			$pondera += 20;
		}
		else
		{
			$pondera += 30;
		}

		return $pondera;
	}

	private function acentos($cadena) 
	{
		$search = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±,Ã“,Ã ,Ã‰,Ã ,Ãš,â€œ,â€ ,Â¿,ü");
		$replace = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ó,Á,É,Í,Ú,\",\",¿,&uuml;");
		$cadena= str_replace($search, $replace, $cadena);

		return $cadena;
	}

	public function inicio()
	{

		//dd(Route::getCurrentRoute()->name);

		/* $convert = Incidente::get();
		foreach ($convert as $c)
		{
			$new = Incidente::find($c->id);
			$new->descripcion = $this->acentos($c->descripcion);
			$new->save();
		} */

		//dd(request()->all());

		
		$filtros = array();
		$usuario = request()->usuario;
		$cliente = request()->cliente;
		$status = request()->status;
		$tipo_incidente = request()->tipo_incidente;
		$modulo = request()->modulo;
		$prioridad = request()->prioridad;
		$buscar = request()->buscar;
		$pagina = request()->p;

		if (isset($_SESSION['filtros']))
		{
			if (isset($_SESSION['filtros']['usuario']) && !$usuario) $usuario = $_SESSION['filtros']['usuario'];
			if (isset($_SESSION['filtros']['cliente']) && !$cliente) $cliente = $_SESSION['filtros']['cliente'];
			if (isset($_SESSION['filtros']['tipo_incidente']) && !$tipo_incidente) $tipo_incidente = $_SESSION['filtros']['tipo_incidente'];
			if (isset($_SESSION['filtros']['modulo']) && !$modulo) $modulo = $_SESSION['filtros']['modulo'];
			if (isset($_SESSION['filtros']['prioridad']) && !$prioridad) $prioridad = $_SESSION['filtros']['prioridad'];
			if (isset($_SESSION['filtros']['status']) && !$status) $status = $_SESSION['filtros']['status'];
			if (isset($_SESSION['filtros']['buscar']) && $buscar!='') $buscar = $_SESSION['filtros']['buscar'];
			if (isset($_SESSION['filtros']['pagina']) && !$pagina) $pagina = $_SESSION['filtros']['pagina'];
		} 

		if ($usuario && $usuario!='todos') $filtros['usuario'] = $usuario;
		if ($cliente && $cliente!='todos') $filtros['cliente'] = $cliente;
		if ($status && $status!='todos') $filtros['status'] = $status;
		if ($tipo_incidente && $tipo_incidente!='todos') $filtros['tipo_incidente'] = $tipo_incidente;
		if ($modulo && $modulo!='todos') $filtros['modulo'] = $modulo;
		if ($prioridad && $prioridad!='todos') $filtros['prioridad'] = $prioridad;
		if ($buscar && $buscar!='') $filtros['buscar'] = $buscar;
		if ($pagina && $pagina!=1) $filtros['pagina'] = $buscar;

		$_SESSION['filtros'] = $filtros;

	
		$data = Incidente::with(['cliente', 'status'])->orderBy('prioridad DESC, id DESC, fecha_ingreso DESC');

		if (isset($buscar) && $buscar!='')
			$data = $data->where('descripcion', 'LIKE', '%'.$buscar.'%');

		if (isset($filtros['usuario']))
			$data = $data->where('asignado', $filtros['usuario']);

		if (isset($filtros['cliente']))
			$data = $data->where('cliente', $filtros['cliente']);

		if (isset($filtros['tipo_incidente']))
			$data = $data->where('tipo_incidente', $filtros['tipo_incidente']);

		if (isset($filtros['modulo']))
			$data = $data->where('modulo', $filtros['modulo']);

		if (isset($filtros['prioridad']))
		{
			if ($filtros['prioridad']=='alta')
				$data = $data->where('prioridad', '>=', 80);

			elseif ($filtros['prioridad']=='media')
				$data = $data->where('prioridad', '>=', 40)->where('prioridad', '<', 80);

			else
				$data = $data->where('prioridad', '<', 40);
		}

		//dd($filtros); //exit();

		if (isset($filtros['status']))
		{
			if ($filtros['status']=='abiertos')
				$data = $data->where('status', '<', 10);

			elseif ($filtros['status']=='finalizados')
				$data = $data->where('status', 10)->orWhere('status', 20);

			elseif ($filtros['status']=='sin_asignar')
				$data = $data->where('status', 0);

			elseif ($filtros['status']=='en_proceso')
				$data = $data->whereBetween('status', [1 ,9]);

			elseif ($filtros['status']=='resueltos')
				$data = $data->where('status', 10);

			elseif ($filtros['status']=='cancelados')
				$data = $data->where('status', 50);

			else
				$data = $data->where('status', 20);
		}

		if (Auth::user()->rol=='cliente')	// Si es un cliente solo puede ver sus tickets
			$data = $data->where('usuario', Auth::user()->Usuario);

		/* elseif (Auth::user()->rol!='soporte')	// Si no es soporte solo puede ver sus tickets creados y asignados
			$data = $data->where('usuario', Auth::user()->Usuario)->orWhere('asignado', Auth::user()->Usuario); */

        $data = $data->paginate(15);

		$sin_asignar = Auth::user()->rol!='cliente'? count(Incidente::whereStatus(0)->get()) : 0;

		$clientes = Cliente::activos()->toArray();
		$usuarios = User::activos()->pluck('nombre', 'Usuario');
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$modulos = Modulo::orderBy('descripcion')->get();

		return view('incidentes', compact('data', 'clientes', 'usuarios',
			'tipo_incidente', 'modulos', 'filtros', 'buscar', 'sin_asignar'));
	}

	public function mostrar($id)
	{
		$data = Incidente::with(['cliente', 'tipo_incidente', 'status'])->find($id);
		//dd($data);exit();
		return view('incidente', compact('data'));
	}

	public function crear()
	{
		$usuarios = User::activos()->pluck('nombre', 'Usuario');
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$modulos = Modulo::orderBy('descripcion')->get();
		$status = StatusIncidente::whereBetween('codigo', [1,19])->get();

		if (Auth::user()->rol=='cliente')
		{
			$cliente = Cliente::with('areas')->where('codigo', Auth::user()->cliente)->get()->toArray();
		}
		else
		{
			$cliente = Cliente::activos()->toArray();
		}
		//dd($cliente); exit();
		//header('Content-type: text/plain; charset=utf-8');

		return view('incidente-crear', compact('cliente', 'tipo_incidente', 'status', 'modulos', 'usuarios'));

	}

	public function guardar(Request $request)
	{	
		$request->validate([
			'descripcion' => 'required'
		]);

		//dd($request->all()); //exit();
		$date = strtotime($request->fecha);

		$inc = array();
		$inc['cliente'] = Auth::user()->cliente==5? $request->cliente : Auth::user()->cliente;
		$inc['area'] = $request->area? $request->area : 0;
		$inc['modulo'] = $request->modulo;
		$inc['programa'] = 0;
		$inc['tipo_incidente'] = $request->tipo_incidente;
		$inc['descripcion'] = $this->acentos($request->descripcion);
		$inc['menu'] = '';
		$inc['usuario'] = Auth::user()->Usuario;
		$inc['punto_menu'] = 0;
		$inc['mail'] = '';
		$inc['tel'] = '';
		$inc['status'] = Auth::user()->cliente==5? $request->status : 0;
		$inc['asignado'] = Auth::user()->cliente==5? $request->asignado : null;
		$inc['fecha_ingreso'] = Auth::user()->cliente==5? date('Y-m-d H:i', $date) : date('Y-m-d H:i');
		
		$inc['prioridad'] = $this->calcularPrioridad($request->cliente, $request->modulo, $request->tipo_incidente);

		//dd($inc); exit();


		$res = Incidente::create($inc);

		if ($res)
		{
			if ($request->hasFile('archivo') && $request->file('archivo')->isValid())
			{
				$name = strtolower($request->file('archivo')->name());
				$path = 'archivos/'.(int)$res->id.'/0';
				$request->file('archivo')->storeAs($path, $name);
	
				$adjunto = new AdjuntosIncidente;
				$adjunto->incidente = $res->id;
				$adjunto->avance = 0;
				$adjunto->adjunto = $name;
				$adjunto->save();
			}

			return to_route('incidentes')->with('message', 'Se generó el incidente correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el incidente');
		}
	}

	public function editar($id)
	{
		$data = Incidente::with(['cliente', 'tipo_incidente', 'status', 'avances'])->find($id);

		$cliente = Cliente::activos()->toArray();
		$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$usuarios = User::activos()->pluck('nombre', 'Usuario');
		$modulos = Modulo::orderBy('descripcion')->get();
		$tipo_avance = TipoAvance::get();
		$status = StatusIncidente::where('descripcion', '!=', 'Sin Asignar')
			->where('descripcion', '!=', 'Cerrado')->get();

		$data->adjunto = AdjuntosIncidente::where('incidente', $id)->where('avance', 0)->first();
		$data->usuario = $usuarios[$data->usuario];

		foreach ($data->avances as $avance)
		{
			$avance->adjunto = AdjuntosIncidente::where('incidente', $id)->where('avance', $avance->id)->first();
		}

		//dd($data);exit();
		//$dato = $data->descripcion;
		//$dato = $this->acentos($dato);
		//$data->descripcion = $this->acentos($data->descripcion);

		return view('incidente-editar', compact('data', 'adjuntos', 'cliente', 'avances', 
				'modulos', 'tipo_incidente','tipo_avance', 'status', 'usuarios'));
	}

	public function modificar(Request $request, $id)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required'
		]);

		$inc = Incidente::find($id);

		$newrequest = $request->all();
		$date = strtotime($request->fecha);
		$newrequest['fecha_ingreso'] = date('Y-m-d H:i', $date);

		$newrequest['prioridad'] = $this->calcularPrioridad($request->cliente, $request->modulo, $request->tipo_incidente);

		$res = $inc->update($newrequest);

		if ($res)
			return back()->with('message', 'Se guardaron los cambios');
		else
			return back()->with('error', 'Hubo un error al guardar los cambios');

	}

	public function cargaMasiva()
	{
		return view('masiva');
	}

	public function procesar(Request $request)
	{
		# Guardamos el archivo a procesar
		$name =  strtolower($request->file('archivo')->name());
        $extension =  strtolower($request->file('archivo')->extension());
        $newfile = Storage::path('archivos').'/'.$name;

        if (!$request->file('archivo')->isValid())
        {
            return back()->with('error', 'Verifique el archivo a procesar');
        }

        $request->file('archivo')->storeAs('archivos', $name);


		# Cargamos el archivo en un array
        $datos = null;
        
        if ($extension == 'xls')
        {
            if ( $xls = SimpleXLS::parse($newfile) ) {
                $datos = $xls->rows();
            } else {
                echo SimpleXLS::parseError();
            }
        }
        elseif ($extension == 'xlsx')
        {
            if ( $xls = SimpleXLSX::parse($newfile) ) {
                $datos = $xls->rows();
            } else {
                echo SimpleXLSX::parseError();
            }
        }
        else
        {
            return back()->with('error', 'No se puede procesar el archivo');
        }


		# Procesamos el archivo usando la cabecera como KEY
		$final = array();
		$cabeceras = array_shift($datos);

		for ($i=0; $i<count($datos); ++$i)
		{
			$linea = array();
			for ($k=0; $k<count($cabeceras); ++$k)
			{
				$cabecera = trim(strtolower($cabeceras[$k]));
				$linea[$cabecera] = (is_string($datos[$i][$k]) && $datos[$i][$k]=='')? null: $datos[$i][$k];
			}
			$final[] = $linea;
		}

		
		//dd($final); 

		# Subimos los datos a MySQL

		$result = true;

		foreach ($final as $dato)
		{

			$date = strtotime(substr($dato['fecha'], 0, 10).' '.substr($dato['hora'], -8));

			$inc = new Incidente;
			$inc->cliente = $this->clientes[$dato['cliente']];
			$inc->area = (int)Cliente::find($inc->cliente)->areas()->first()->codigo;
			$inc->modulo = 65;
			$inc->programa = 0;
			$inc->tipo_incidente = 16;
			$inc->descripcion = $dato['descripcion'];
			$inc->menu = '';
			$inc->usuario = Auth::user()->Usuario; //'lisandro';
			$inc->asignado = Auth::user()->Usuario;
			$inc->punto_menu = 0;
			$inc->mail = '';
			$inc->tel = '';
			$inc->status = 10;
			$inc->fecha_ingreso = date('Y-m-d H:i', $date);
			$inc->prioridad = 40;

			//dd($inc); exit();
			if (!$inc->save())
				$result = false;
		}

		if ($result)
			return back()->with('message', 'Se guardaron los registros correctamente');
		else
			return back()->with('error', 'Hubo un error al guardar los registros');

	}

	public function descargarExcel()
	{
		Storage::download("plantilla_incidentes.xlsx");
	}

	public function guardarAvance(Request $request, $id)
	{
		//dd($request);
		$avance = array();
		$avance['incidente'] = (int)$id;
		$avance['tipo_avance'] = (int)$request->tipo_avance;
		$avance['descripcion'] = $request->descripcion;
		$avance['usuario'] = Auth::user()->Usuario;
		$avance['fecha_ingreso'] = date('Y-m-d H:i');
		
		$res = Avance::create($avance);

		if ($res)
		{
			if ($request->hasFile('archivo'))
			{
				if ($request->file('archivo')->isValid())
				{
					$name = strtolower($request->file('archivo')->name());
					$path = 'archivos/'.(int)$id.'/'.$res->id;
					$request->file('archivo')->storeAs($path, $name);
		
					$adjunto = new AdjuntosIncidente;
					$adjunto->incidente = $id;
					$adjunto->avance = $res->id;
					$adjunto->adjunto = $name;
					$adjunto->save();
				}
			}

			$inc = Incidente::find($id);

			if (Auth::user()->Usuario != $request->usuario)
				$inc->asignado = $request->usuario;

			if ((int)$request->tipo_avance==1) # Tomado por responsable
				$inc->asignado = $request->usuario;

			elseif ((int)$request->tipo_avance==10) # Incidente resuelto
				$inc->status = 10;

			elseif ((int)$request->tipo_avance==20) # Incidente cerrado
				$inc->status = 20;

			elseif ((int)$request->tipo_avance==9) # A la espera de aprobacion
				$inc->status = 5;
				
			elseif ((int)$request->tipo_avance==50) # Incidente cancelado
				$inc->status = 50;

			elseif ((int)$request->tipo_avance!=4) # Todo lo demas excepto Observacion se pasa a 'En proceso'
				$inc->status = 1;


			$inc->save();

			return back()->with('message', 'Se guardó el avance correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el avance');
		}


	}

	public function guardarNota(Request $request, $id)
	{
		//dd($request);
		$avance = array();
		$avance['incidente'] = (int)$id;
		$avance['tipo_avance'] = Auth::user()->rol=='cliente'? 30 : 100;
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

			$inc = Incidente::find($id);

			return back()->with('message', 'Se guardó la correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar la nota');
		}


	}

	public function descargarAdjunto($incidente, $avance, $archivo)
	{
		Storage::download("archivos/$incidente/$avance/$archivo");
	}

	public function informes()
	{
		$clientes = Cliente::activos();
		//$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$status = StatusIncidente::orderBy('descripcion')->get();
		$usuarios = User::activos()->pluck('nombre', 'Usuario');

		return view('informes', compact('clientes', 'status', 'usuarios'));
	}

	public function procesarInforme(Request $request)
	{
		//dd($request->all());

		$inicio = date('Y-m-d H:i', strtotime($request->fecha_desde));
		$fin = date('Y-m-d H:i', strtotime('+1 day', strtotime($request->fecha_hasta)));
		
		$filtros = array();
		$filtros['fecha_desde'] = $request->fecha_desde;
		$filtros['fecha_hasta'] = $request->fecha_hasta;

		$data = Incidente::with(['cliente', 'tipo_incidente', 'status', 'avances'])
			->whereBetween('fecha_ingreso', [$inicio, $fin]);

		if ($request->asignado != 'todos')
		{
			$data = $data->where('asignado', $request->asignado);
			$filtros['asignado'] = User::where('Usuario', $request->asignado)->first()->nombre;
		}

		if ($request->cliente != 'todos')
		{
			$data = $data->where('cliente', $request->cliente);
			$filtros['cliente'] = $request->cliente; //Cliente::find($request->cliente)->descripcion;
		}

		if ($request->estado != 'todos' && $request->estado != 'todosinc')
		{
			$data = $data->where('status', $request->estado);
			$filtros['estado'] = StatusIncidente::find($request->estado)->descripcion;
		}
		elseif ($request->estado == 'todos')
		{
			$data = $data->where('status', '<', 50);
		}
		
		$data = $data->paginate(20);

		foreach ($data as $dato)
			$dato->fecha_ingreso = date('d-m-Y', strtotime($dato->fecha_ingreso));

		$_SESSION['informe_resultado'] = $filtros;

		return view('informes-resultado', compact('data', 'filtros'));

	}

	public function descargarInforme()
	{
		$datos = $_SESSION['informe_resultado'];

		$inicio = date('Y-m-d H:i', strtotime($datos['fecha_desde']));
		$fin = date('Y-m-d H:i', strtotime('+1 day', strtotime($datos['fecha_hasta'])));
		
		$data = Incidente::with(['cliente', 'tipo_incidente', 'status', 'avances'])
			->selectRaw('incidentes.*, areas.descripcion as area_desc, modulos.descripcion as modulo_desc')
			->whereBetween('fecha_ingreso', [$inicio, $fin])
			->where('status', '<', 50)
			->leftJoin('areas', 'codigo', '=', 'area')
			->leftJoin('modulos', 'codigo', '=', 'modulo');

		if (isset($datos['asignado']))
			$data = $data->where('asignado', $datos['asignado']);

		if (isset($datos['cliente']))
			$data = $data->where('cliente', $datos['cliente']);

		if (isset($datos['estado']))
			$data = $data->where('status', $datos['estado']);

		/* dd($inicio); dd($fin);
		dd($datos['asignado']);
		dd($datos['cliente']);
		dd($datos['estado']); */

		$data = $data->get();

		$usuarios = array();
		foreach($data->pluck('asignado') as $user)
		{
			if (!in_array($user, $usuarios)) $usuarios[] = $user;
		}

		$usuarios = User::whereIn('Usuario', implode(',',$usuarios))->get()->pluck('nombre', 'Usuario');
		
		/* $export = '';
		$export .= '
			<table> 
			<tr> 
			<th>INC</th>
			<th>Fecha</th> 
			<th>Cliente</th> 
			<th>Area</th> 
			<th>Modulo</th> 
			<th>Tipo</th> 
			<th>Estado</th> 
			<th>Usuario</th> 
			<th>Descripcion</th> 
			<th>Detalle</th> 
			</tr>
			';

		foreach ($data as $dato)
		{
			$fecha = date('d-m-Y', strtotime($dato->fecha_ingreso));
			$export .= '
			<tr>
			<td>'.$dato->id.'</td> 
			<td>'.$fecha.'</td> 
			<td>'.$dato->cliente->descripcion.'</td> 
			<td>'.$dato->tipo_incidente->descripcion.'</td> 
			<td>'.$dato->status->descripcion.'</td> 
			<td>'.$usuarios[$dato->asignado].'</td> 
			<td>'.$dato->short.'</td> 
			<td>'.$dato->large.'</td> 
			</tr>
			';
		}
		$export .= '</table>';
		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename=Informe_incidentes.xlsx');
		header("Pragma: no-cache");
    	header("Expires: 0");
		echo $export;
		exit(); */



		$salida = "INC;Fecha;Cliente;Area;Modulo;Tipo de incidente;Estado;Usuario;Descripcion;Detalle\n";

		foreach ($data as $dato)
		{
			$fecha = date('d-m-Y', strtotime($dato->fecha_ingreso));
			$salida .= $dato->id . ';' . $fecha . ';';
			$salida .= $dato->cliente->descripcion . ';';
			$salida .= $dato->area_desc . ';';
			$salida .= $dato->modulo_desc . ';';			
			$salida .= $dato->tipo_incidente->descripcion . ';';
			$salida .= $dato->status->descripcion . ';'; 
			$salida .= $usuarios[$dato->asignado] . ';';
			$salida .= $dato->short . ';';
			$salida .= $dato->large . "\n";
		}

		Storage::put('Informe_incidentes.csv', $salida);
		Storage::download('Informe_incidentes.csv');


	}

}