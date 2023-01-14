<?php

class TipoIncidenteController extends Controller
{

	public function index()
	{
		//$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();	

		$tipo_incidente = TipoIncidente::withCount('incidentes')
			->orderBy('descripcion')
			->paginate(20);

		return view('admin.tipoincidente', compact('tipo_incidente'));
	}

	public function create()
	{
		return view('admin.tipoincidente-crear');
	}

	public function store(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:tipo_incidentes,descripcion'
		]);

		$tipo = new TipoIncidente;
		$tipo->descripcion = $request->descripcion;
		$tipo->pondera = $request->prioridad;
		$tipo->sla = $request->sla;
		$tipo->activo = 1;
		$res = $tipo->save();

		if ($res)
		{
			return to_route('tipoincidentes.index')->with('message', 'Se guardó el tipo de incidente correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el tipo de incidente');
		}

	}

	public function edit($id)
	{
		$tipo_incidente = TipoIncidente::findOrFail($id);
		return view('admin.tipoincidente-editar', compact('tipo_incidente'));
	}

	public function update(Request $request, $id)
	{
		$tipo_incidente = TipoIncidente::findOrFail($id);
		$tipo_incidente->descripcion = $request->descripcion;
		$tipo_incidente->pondera = $request->prioridad;
		$tipo_incidente->sla = $request->sla;
		$res = $tipo_incidente->save();

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
		$tipo_incidente = TipoIncidente::findOrFail($id);

		//dd($tipo_incidente); exit();

		if ($tipo_incidente->delete())
		{
			return back()->with('message', 'Se eliminó el área seleccionada');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el área');
		}
	}

	public function habilitar($id)
	{
		$tipo_incidente = TipoIncidente::findOrFail($id);
		$tipo_incidente->activo = $tipo_incidente->activo? 0 : 1;
		$tipo_incidente->save();

		return response($tipo_incidente);
	}


}