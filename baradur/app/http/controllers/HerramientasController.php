<?php

class HerramientasController extends Controller
{

	public function herramientas() 
	{
		$this->authorize('isadmin');

		return view('herramientas');
	}

	public function resolver(Request $request)
	{
		if ($request->fecha_resolucion)
		{

			# Crear avances para resolver los incidentes
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

	public function cerrar(Request $request)
	{
		
		if ($request->fecha_cierre)
		{

			# Crear avances para cerrar los incidentes
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
}