<?php

class AreasController extends Controller
{
	public function index()
	{
		//$areas = Area::orderBy('descripcion')->get();
		
		$areas = Area::selectRaw('areas.*, COALESCE(i.cnt,0) AS contador')
		->joinSub('SELECT area, count(area) cnt FROM incidentes GROUP BY area', 'i',
			'i.area', '=', 'areas.codigo', 'LEFT')
		->orderBy('descripcion')
		->get();

		return view('admin.areas', compact('areas'));
	}

	public function eliminarArea($id)
	{
		$area = Area::find($id);

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

	public function crearArea()
	{
		return view('admin.area-crear');
	}

	public function guardarArea(Request $request)
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
			return back()->with('message', 'Se guardó el área correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el área');
		}

	}

	public function editarArea($id)
	{
		$area = Area::find($id);
		return view('admin.area-editar', compact('area'));
	}

	public function modificarArea(Request $request, $id)
	{
		$area = Area::find($id);
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


}