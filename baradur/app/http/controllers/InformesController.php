<?php

class InformesController extends Controller
{
	
	public function informes()
	{
		$this->authorize('informes');

		$clientes = Cliente::activos()->get();
		//$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();
		$status = StatusIncidente::orderBy('descripcion')->get();
		$usuarios = User::activos()->where('tipo', 1)->get()->keys(['nombre', 'Usuario'])->toArray();
		$grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();

		return view('informes', compact('clientes', 'status', 'usuarios', 'grupos'));
	}

	public function procesarInforme(Request $request)
	{
		$this->authorize('informes');

		$inicio = Carbon::parse($request->fecha_desde)->startOfDay();
		$fin = Carbon::parse($request->fecha_hasta)->endOfDay();

		$filtros = array();
		$filtros['fecha_desde'] = $request->fecha_desde;
		$filtros['fecha_hasta'] = $request->fecha_hasta;

		$data = Incidente::with(['inc_cliente', 'tipo_incidente', 'estado', 'avances'])
			->whereBetween('fecha_ingreso', [$inicio, $fin]);

		if ($request->grupo != 'todos')
		{
			$data = $data->where('grupo', $request->grupo);
			$filtros['grupo'] = $request->grupo;
			$filtros['grupo_desc'] = Grupo::find($request->grupo)->first()->descripcion;
		}

		if ($request->asignado != 'todos')
		{
			$data = $data->where('asignado', $request->asignado);
			$filtros['asignado'] = $request->asignado;
			$filtros['asignado_desc'] = User::where('Usuario', $request->asignado)->first()->nombre;
		}

		if ($request->cliente != 'todos')
		{
			$data = $data->where('cliente', $request->cliente);
			$filtros['cliente'] = $request->cliente;
			$filtros['cliente_desc'] = Cliente::find($request->cliente)->descripcion;
		}

		if ($request->estado != 'todos' && $request->estado != 'todosinc')
		{
			$data = $data->where('status', $request->estado);
			$filtros['estado'] = $request->estado;
			$filtros['estado_desc'] = StatusIncidente::find($request->estado)->descripcion; 
		}
		else
		{
			if ($request->estado == 'todos')
			{
				$data = $data->where('status', '<', 50);
				$filtros['estado_desc'] = 'Todos (excluír cancelados)'; 
			}
			else
			{
				$filtros['estado_desc'] = 'Todos (incluír cancelados)'; 
			}
			$filtros['estado'] = $request->estado;
		}

		$data = $data->paginate(20);

		foreach ($data as $dato)
			$dato->fecha_ingreso = Carbon::parse($dato->fecha_ingreso)->rawFormat('d-m-Y');

		$_SESSION['informe_resultado'] = $filtros;

		return view('informes-resultado', compact('data', 'filtros'));

	}

	public function descargarInforme()
	{
		$datos = $_SESSION['informe_resultado'];

		//dd($datos); exit();

		$inicio = Carbon::parse($datos['fecha_desde'])->startOfDay();
		$fin = Carbon::parse($datos['fecha_hasta'])->endOfDay();
		
		$data = Incidente::with('avances_resumido')
			->selectRaw('incidentes.*, areas.descripcion as area_desc, modulos.descripcion as modulo_desc,
				clientes.descripcion as cliente_desc, tipo_incidentes.descripcion as tipo_incidente_desc,
				status_incidentes.descripcion as status_desc, usuarios.nombre as usuario_desc,
				grupos.descripcion as grupo_desc')
			->whereBetween('fecha_ingreso', [$inicio, $fin])
			->leftJoin('areas', 'areas.codigo', '=', 'incidentes.area')
			->leftJoin('modulos', 'modulos.codigo', '=', 'incidentes.modulo')
			->leftJoin('clientes', 'clientes.codigo', '=', 'incidentes.cliente')
			->leftJoin('status_incidentes', 'status_incidentes.codigo', '=', 'incidentes.status')
			->leftJoin('tipo_incidentes', 'tipo_incidentes.codigo', '=', 'incidentes.tipo_incidente')
			->leftJoin('usuarios', 'usuarios.Usuario', '=', 'incidentes.usuario')
			->leftJoin('grupos', 'grupos.codigo', '=', 'incidentes.grupo');

		
		if (isset($datos['grupo']))
			$data = $data->where('grupo', $datos['grupo']);

		if (isset($datos['asignado']))
			$data = $data->where('asignado', $datos['asignado']);

		if (isset($datos['cliente']))
			$data = $data->where('cliente', $datos['cliente']);

		if (isset($datos['estado']))
		{
			if ($datos['estado']!='todos' && $datos['estado']!='todosinc')			
			{
				$data = $data->where('status', $datos['estado']);
			}
			elseif ($datos['estado']=='todos')
			{
				$data = $data->where('status', '<', 50);
			}
		}

		$data = $data->orderBy('id')->get();

		foreach ($data as $inc)
		{
			foreach($inc->avances_resumido as $avance)
			{
				if ($avance->tipo_avance == 10)
				{
					# Agregamos la fecha de resolucion
					$inc->resolucion = $avance->fecha_ingreso;

					# Calculamos el tiempo del ticket
					$inc->tiempo = round((strtotime($inc->resolucion) - strtotime($inc->fecha_ingreso))/3600, 2);
				}
				elseif ($avance->tipo_avance == 20)
				{
					# Agregamos la fecha de resolucion/cierre
					$inc->cierre = $avance->fecha_ingreso;

					# Calculamos el tiempo del ticket
					# Si ya se calculó al resolverse no se cambia
					if (!isset($inc->tiempo))
					{
						$inc->tiempo = round((strtotime($inc->cierre) - strtotime($inc->fecha_ingreso))/3600, 2);
					}
				}

			}
			
		}
		
		//dd($data);


		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'INC');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'APERTURA');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'CLIENTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'AREA');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'MODULO');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'TIPO DE INCIDENTE');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'ESTADO');
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'GRUPO');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'USUARIO');
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'DESCRIPCION');
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'DETALLE');
		//$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'RESOLUCION');
		//$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'CIERRE');
		//$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'SLA');

		$objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setBold(true);

		$rowCount = 2;
		foreach ($data as $dato)
		{
			$fecha = Carbon::parse($dato->fecha_ingreso)->rawFormat('d-m-Y H:i');

			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, str_pad($dato->id, 7, '0', STR_PAD_LEFT));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $fecha);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $dato->cliente_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $dato->area_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $dato->modulo_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $dato->tipo_incidente_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $dato->status_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $dato->grupo_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $dato->usuario_desc);
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $dato->titulo);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $dato->descripcion);
			//$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $dato->resolucion);
			//$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $dato->cierre);
			//$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $sla);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.$rowCount)->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getRowDimension($rowCount)->setRowHeight(110);

			$rowCount++;
		}
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(60);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

		if (isset($datos['cliente']))
			$objPHPExcel->getActiveSheet()->removeColumn('C');


		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		if (Storage::exists('Informe_incidentes.xls'))
			Storage::delete('Informe_incidentes.xls');

		$objWriter->save(Storage::path('Informe_incidentes.xls'));

		Storage::download('Informe_incidentes.xls');

		/* $objWriter->save('/tmp/Informe_incidentes.xls');
		$mime = mime_content_type('/tmp/Informe_incidentes.xls');

        header('Content-type: '.$mime);
        header('Content-disposition: download; filename="Informe_incidentes.xls"');
        header('content-Transfer-Encoding:binary');
        header('Accept-Ranges:bytes');
        @readfile('/tmp/Informe_incidentes.xls');
        exit(); */

	}

}