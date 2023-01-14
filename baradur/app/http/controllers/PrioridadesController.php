<?php

class PrioridadesController extends Controller
{
	public function index()
	{
		$this->authorize('isadmin');

		$prioridades = Prioridad::all();
		$reglas = Regla::paginate(20);

		return view('admin.prioridades', compact('prioridades', 'reglas'));
	}

	public function editarPrioridad($id)
	{
		$this->authorize('isadmin');

		$prioridad = Prioridad::findOrFail($id);
		
		return view('admin.prioridades-editar', compact('prioridad'));
	}

	public function modificarPrioridad(Request $request, $id)
	{
		$this->authorize('isadmin');

		$prioridad = Prioridad::findOrFail($id);
		$prioridad->descripcion = $request->descripcion;
		$prioridad->minimo = (int)$request->minimo;
		$prioridad->maximo = (int)$request->maximo;

		$res = $prioridad->save();

		if ($res)
		{
			return back()->with('message', 'Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}
	}


}