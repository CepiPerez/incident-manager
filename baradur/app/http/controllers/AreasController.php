<?php

class AreasController extends Controller
{

	public function index()
	{
		//$areas = Area::orderBy('descripcion')->get();
		
		$areas = Area::withCount('incidentes')
			->orderBy('descripcion')
			->paginate(20);

		return view('admin.areas', compact('areas'));
	}

	public function create()
	{
		return view('admin.area-crear');
	}

	public function store(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:areas,descripcion'
		]);

		$tipo = new Area;
		$tipo->descripcion = $request->descripcion;
		$res = $tipo->save();

		if ($res)
		{
			return to_route('areas.index')->with('message', 'Se guardó el área correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el área');
		}

	}

	public function edit($id)
	{
		$area = Area::findOrFail($id);
		return view('admin.area-editar', compact('area'));
	}

	public function update(Request $request, $id)
	{
		$area = Area::findOrFail($id);
		$area->descripcion = $request->descripcion;
		$res = $area->save();

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
		$area = Area::findOrFail($id);

		//dd($cliente); exit();

		if ($area->delete())
		{
			return back()->with('message', 'Se eliminó el área seleccionada');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el área');
		}
	}

}