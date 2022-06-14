<?php

class ModulosController extends Controller
{
	public function index()
	{
		//$modulo = Modulo::orderBy('descripcion')->get();
		$modulo = Modulo::selectRaw('modulos.*, COALESCE(i.cnt,0) AS contador')
		->joinSub('SELECT modulo, count(modulo) cnt FROM incidentes GROUP BY modulo', 'i',
			'i.modulo', '=', 'modulos.codigo', 'LEFT')
		->orderBy('descripcion')
		->get();

		return view('admin.modulos', compact('modulo'));
	}

	public function eliminarModulo($id)
	{
		$modulo = Modulo::find($id);

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

	public function crearModulo()
	{
		return view('admin.modulo-crear');
	}

	public function guardarModulo(Request $request)
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
			return back()->with('message', 'Se guardó el módulo correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el módulo');
		}

	}

	public function editarModulo($id)
	{
		$modulo = Modulo::find($id);
		return view('admin.modulo-editar', compact('modulo'));
	}

	public function modificarModulo(Request $request, $id)
	{
		$modulo = Modulo::find($id);
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

	public function habilitarModulo($id)
	{
		$modulo = Modulo::find($id);
		$modulo->activo = $modulo->activo? 0 : 1;
		$modulo->save();

		return response($modulo);
	}


}