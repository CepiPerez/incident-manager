<?php

class HerramientasController extends Controller
{

	public function herramientas() 
	{
		$this->authorize('isadmin');

		return view('herramientas');
	}

	# Crear avances para resolver los incidentes
	public function resolver(Request $request)
	{
		if ($request->fecha_resolucion)
		{
			$fin = date('Y-m-d H:i', strtotime('+1 day', strtotime($request->fecha_resolucion)));
			$convert = Incidente::where('fecha_ingreso', '<', $fin)
				->where('status', 1)
				->get();

			foreach ($convert as $c)
			{
				$avance = array();
				$avance['incidente'] = (int)$c->id;
				$avance['tipo_avance'] = 10;
				$avance['descripcion'] = '';
				$avance['usuario'] = $c->usuario;
				$avance['fecha_ingreso'] = date('Y-m-d H:i', strtotime($c->fecha_ingreso . ' + 1 hour'));
				$avance['asignado_prev'] = $c->asignado;
				$avance['status_prev'] = $c->status;

				$res = Avance::create($avance);

				$new = Incidente::find($c->id);
				$new->status = 10;
				$new->save();
			}

			return back()->with('message', 'Se procesaron los datos correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al procesar los datos');
		}
	}

	# Crear avances para cerrar los incidentes
	# Solamente cierra los incidentes resueltos
	public function cerrar(Request $request)
	{		
		if ($request->fecha_cierre)
		{
			$fin = date('Y-m-d H:i', strtotime('+1 day', strtotime($request->fecha_cierre)));
			$convert = Incidente::where('fecha_ingreso', '<', $fin)
				->where('status', 10)
				->get();
		
			foreach ($convert as $c)
			{
				$avance = array();
				$avance['incidente'] = (int)$c->id;
				$avance['tipo_avance'] = 20;
				$avance['descripcion'] = 'Cierre automatico del incidente';
				$avance['usuario'] = 'admin';
				$avance['fecha_ingreso'] = date('Y-m-d H:i', strtotime($c->fecha_ingreso . ' + 49 hour'));
				$avance['asignado_prev'] = $c->asignado;
				$avance['status_prev'] = $c->status;

				$res = Avance::create($avance);

				$new = Incidente::find($c->id);
				$new->status = 20;
				$new->save();
			}
		
			return back()->with('message', 'Se procesaron los datos correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al procesar los datos');
		}
	}


	public function buscarIncidente()
	{
		$current = request()->incident;
		$search = request()->search;

		$data = Incidente::selectRaw("incidentes.id")
			->where('status', '<', 10)
			->where('id', 'like', "%$search%")
			->where('id', '!=', "$current")
			->orderByDesc('id')
			->paginate(20);

		$response = [];
		$response[] = [
			'id' => 0, 
			'text' => 'Sin dependencia'
		];

		foreach($data as $r){
			$response[] = [
				"id"    => $r->id,
				"text"  => 'Incidente ' . str_pad($r->id, 7, '0', STR_PAD_LEFT)
			];
		}

		return response()->json($response, 200); 
	}


}