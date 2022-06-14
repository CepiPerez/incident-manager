<?php

class TipoIncidenteController extends Controller
{
	public function index()
	{
		//$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();	

		$tipo_incidente = TipoIncidente::selectRaw('tipo_incidentes.*, COALESCE(i.cnt,0) AS contador')
		->joinSub('SELECT tipo_incidente, count(tipo_incidente) cnt FROM incidentes GROUP BY tipo_incidente', 'i',
			'i.tipo_incidente', '=', 'tipo_incidentes.codigo', 'LEFT')
		->orderBy('descripcion')
		->get();

		return view('admin.tipoincidente', compact('tipo_incidente'));
	}

	public function eliminarTipoIncidente($id)
	{
		$tipo_incidente = TipoIncidente::find($id);

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

	public function crearTipoIncidente()
	{
		return view('admin.tipoincidente-crear');
	}

	public function guardarTipoIncidente(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:tipo_incidentes,descripcion'
		]);

		$tipo = new TipoIncidente;
		$tipo->descripcion = $request->descripcion;
		$tipo->pondera = $request->prioridad;
		$tipo->activo = 1;
		$res = $tipo->save();

		if ($res)
		{
			return back()->with('message', 'Se guardó el tipo de incidente correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el tipo de incidente');
		}

	}

	public function editarTipoIncidente($id)
	{
		$tipo_incidente = TipoIncidente::find($id);
		return view('admin.tipoincidente-editar', compact('tipo_incidente'));
	}

	public function modificarTipoIncidente(Request $request, $id)
	{
		$tipo_incidente = TipoIncidente::find($id);
		$tipo_incidente->descripcion = $request->descripcion;
		$tipo_incidente->pondera = $request->prioridad;
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

	public function habilitarTipoIncidente($id)
	{
		$tipo_incidente = TipoIncidente::find($id);
		$tipo_incidente->activo = $tipo_incidente->activo? 0 : 1;
		$tipo_incidente->save();

		return response($tipo_incidente);
	}


}