<?php

class TipoServicioController extends Controller
{
	public function index()
	{
		//$tipo_servicio = TipoServicio::orderBy('descripcion')->get();	
		
		$tipo_servicio = TipoServicio::withCount('clientes')
			->orderBy('descripcion')
			->paginate(20);

		return view('admin.tiposervicio', compact('tipo_servicio'));
	}

	public function create()
	{
		return view('admin.tiposervicio-crear');
	}

	public function store(Request $request)
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
			return to_route('tiposervicios.index')->with('message', 'Se guardó el tipo de incidente correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el tipo de incidente');
		}

	}

	public function edit($id)
	{
		$tipo_servicio = TipoServicio::findOrFail($id);
		return view('admin.tiposervicio-editar', compact('tipo_servicio'));
	}

	public function update(Request $request, $id)
	{
		//dd($request->all()); exit();

		$tipo_servicio = TipoServicio::findOrFail($id);
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

	public function destroy($id)
	{
		$tipo_servicio = TipoServicio::findOrFail($id);

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

	public function habilitar($id)
	{
		$tipo_servicio = TipoServicio::findOrFail($id);
		$tipo_servicio->activo = $tipo_servicio->activo? 0 : 1;
		$tipo_servicio->save();

		return response($tipo_servicio);
	}


}