<?php

class TipoServicioController extends Controller
{
	public function index()
	{
		//$tipo_servicio = TipoServicio::orderBy('descripcion')->get();	

		$tipo_servicio = TipoServicio::selectRaw('tipo_servicios.*, COALESCE(i.cnt,0) AS contador')
		->joinSub('SELECT tipo_servicio, count(tipo_servicio) cnt FROM clientes GROUP BY tipo_servicio', 'i',
			'i.tipo_servicio', '=', 'tipo_servicios.codigo', 'LEFT')
		->orderBy('descripcion')
		->get();

		return view('admin.tiposervicio', compact('tipo_servicio'));
	}

	public function eliminarTipoServicio($id)
	{
		$tipo_servicio = TipoServicio::find($id);

		//dd($tipo_servicio); exit();

		if ($tipo_servicio->delete())
		{
			return back()->with('message', 'Se eliminó el área seleccionada');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el área');
		}
	}

	public function crearTipoServicio()
	{
		return view('admin.tiposervicio-crear');
	}

	public function guardarTipoServicio(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:tipo_servicios,descripcion'
		]);

		$tipo = new TipoServicio;
		$tipo->descripcion = $request->descripcion;
		$tipo->pondera = $request->prioridad;
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

	public function editarTipoServicio($id)
	{
		$tipo_servicio = TipoServicio::find($id);
		return view('admin.tiposervicio-editar', compact('tipo_servicio'));
	}

	public function modificarTipoServicio(Request $request, $id)
	{
		//dd($request->all()); exit();

		$tipo_servicio = TipoServicio::find($id);
		$tipo_servicio->descripcion = $request->descripcion;
		$tipo_servicio->pondera = $request->prioridad;
		$res = $tipo_servicio->save();

		if ($res)
		{
			return back()->with('message', 'Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}
	}

	public function habilitarTipoServicio($id)
	{
		$tipo_servicio = TipoServicio::find($id);
		$tipo_servicio->activo = $tipo_servicio->activo? 0 : 1;
		$tipo_servicio->save();

		return response($tipo_servicio);
	}


}