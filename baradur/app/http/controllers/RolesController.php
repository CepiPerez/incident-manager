<?php

class RolesController extends Controller
{

	public function index()
	{
		$roles = Rol::withCount('usuarios')
			->orderBy('descripcion')
			->paginate(20);

		return view('admin.roles', compact('roles'));
	}

	public function create()
	{
		$permisos = Permiso::all();

		return view('admin.rol-crear', compact('permisos'));
	}

	public function store(Request $request)
	{

		$request->validate([
			'descripcion' => 'required|max:50|unique:roles,descripcion'
		]);

		$rol = new Rol;
		$rol->descripcion = $request->descripcion;
		$rol->tipo = $request->tipo;
		$res = $rol->save();

		$permisos = array();

		if ($request->permisos)
		{
			foreach ($request->permisos as $key => $val)
			{
				$permisos[] = $val;
			}
			if ($request->permisosadm)
			{
				foreach ($request->permisosadm as $key => $val)
				{
					if ($request->tipo==1)
					{
						$permisos[] = $val;
					}
				}
			}
		}

		$rol->permisos()->sync($permisos);

		if ($res)
		{
			return to_route('roles.index')->with('message', 'Se guardó el rol correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el rol');
		}

	}

	public function edit($id)
	{
		$rol = Rol::findOrFail($id);

		$permisos = Permiso::all();
		$permisos_rol = $rol->permisos->pluck('id')->toArray();

		return view('admin.rol-editar', compact('rol', 'permisos', 'permisos_rol'));
	}

	public function update(Request $request, $id)
	{
		$rol = Rol::findOrFail($id);
		$rol->descripcion = $request->descripcion;
		$rol->tipo = $request->tipo;
		$res = $rol->save();

		$permisos = array();

		if ($request->permisos)
		{
			foreach ($request->permisos as $key => $val)
			{
				$permisos[] = $val;
			}

			if ($request->permisosadm)
			{
				foreach ($request->permisosadm as $key => $val)
				{
					if ($request->tipo==1)
					{
						$permisos[] = $val;
					}
				}
			}
		}

		$rol->permisos()->sync($permisos);

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