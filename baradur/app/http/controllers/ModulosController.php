<?php

class ModulosController extends Controller
{

	public function index()
	{
		//$modulo = Modulo::orderBy('descripcion')->get();

		$modulo = Modulo::withCount('incidentes')
			->orderBy('descripcion')
			->paginate(20);

		return view('admin.modulos', compact('modulo'));
	}

	public function create()
	{
		return view('admin.modulo-crear');
	}

	public function store(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:modulos,descripcion'
		]);

		$modulo = new Modulo;
		$modulo->descripcion = $request->descripcion;
		$modulo->pondera = $request->prioridad;
		$modulo->activo = 1;
		$res = $modulo->save();

		if ($res)
		{
			return to_route('modulos.index')->with('message', 'Se guardó el módulo correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el módulo');
		}

	}

	public function edit($id)
	{
		$modulo = Modulo::findOrFail($id);
		return view('admin.modulo-editar', compact('modulo'));
	}

	public function update(Request $request, $id)
	{
		$modulo = Modulo::findOrFail($id);
		$modulo->descripcion = $request->descripcion;
		$modulo->pondera = $request->prioridad;
		$res = $modulo->save();

		if ($res)
		{
			return back()->with('message', 'Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}
	}

	public function destroy($id)
	{
		$modulo = Modulo::findOrFail($id);

		//dd($cliente); exit();

		if ($modulo->delete())
		{
			return back()->with('message', 'Se eliminó el módulo seleccionado');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el módulo');
		}
	}

	public function habilitar($id)
	{
		$modulo = Modulo::findOrFail($id);
		$modulo->activo = $modulo->activo? 0 : 1;
		$modulo->save();

		return response($modulo);
	}

}