<?php

class UsuariosController extends Controller
{

	public function index()
	{
		$buscar = request()->buscar && request()->buscar!=''? request()->buscar : null;

		$usuarios = User::with('roles')->withCount(['creados', 'asignados', 'remitente']);

		if ($buscar)
		{
			$usuarios = $usuarios->where('Usuario', 'LIKE', '%'.$buscar.'%')
				->orWhere('nombre', 'LIKE', '%'.$buscar.'%');
		}

		$usuarios = $usuarios->orderBy('Usuario')->paginate(20);

		$permisos = Auth::user()->roles->permisos->pluck('id')->toArray();

		foreach ($usuarios as $user)
		{
			$user->contador = $user->creados_count + $user->asignados_count + $user->remitente_count;
		}

		return view('admin.usuarios', compact('usuarios', 'buscar', 'permisos'));
	}

	public function create()
	{
		$clientes = Cliente::activos()->get();
		$roles = Rol::all();
		return view('admin.usuario-crear', compact('clientes', 'roles'));
	}

	public function store(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'usuario' => 'required|max:20|unique:usuarios,Usuario',
			'nombre' => 'required|max:50',
			'clave' => 'required|max:10'
		]);

		$usuario = new User;
		$usuario->Usuario = $request->usuario;
		$usuario->nombre = $request->nombre;
		$usuario->Password = $request->clave;
		$usuario->Mail = $request->email;
		$usuario->rol = $request->rol;
		$usuario->tipo = $request->tipo;
		$usuario->cliente = $request->tipo==0? $request->cliente : 0;
		$usuario->activo = 1;
		$usuario->status = 1;
		$res = $usuario->save();

		if ($res)
		{
			return to_route('usuarios.index')->withMessage('Se guardó el usuario correctamente');
		}
		else
		{
			return back()->withError('Hubo un error al guardar el usuario');
		}

	}

	public function edit($id)
	{
		$usuario = User::findOrFail($id);
		$clientes = Cliente::activos()->get();
		$roles = Rol::all();
		return view('admin.usuario-editar', compact('usuario', 'clientes', 'roles'));
	}

	public function update(Request $request, $id)
	{
		//dd($request->all()); exit();

		$usuario = User::findOrFail($id);

		$request->validate([
			'usuario' => 'required|max:20|unique:usuarios,Usuario,'.$usuario->Usuario,
			'nombre' => 'required|max:50'
		]);
		
		$previo = null;
		if ($usuario->Usuario != $request->usuario)
		{
			$previo = $usuario->Usuario;
		}


		$usuario->Usuario = $request->usuario;
		$usuario->nombre = $request->nombre;
		$usuario->Mail = $request->email;
		$usuario->tipo = $request->tipo;
		$usuario->cliente = $request->tipo==0? $request->cliente : null;
		$usuario->Password = $request->clave? $request->clave : $usuario->Password;
		$usuario->rol = $request->rol;
		$res = $usuario->save();

		if ($previo)
		{
			DB::statement("UPDATE avances set usuario='$request->usuario' WHERE usuario='$previo'");
			DB::statement("UPDATE avances set destino='$request->usuario' WHERE destino='$previo'");
			DB::statement("UPDATE avances set asignado_prev='$request->usuario' WHERE asignado_prev='$previo'");
			DB::statement("UPDATE asignaciones set usuario='$request->usuario' WHERE usuario='$previo'");
			DB::statement("UPDATE incidentes set usuario='$request->usuario' WHERE usuario='$previo'");
			DB::statement("UPDATE incidentes set remitente='$request->usuario' WHERE remitente='$previo'");
			DB::statement("UPDATE incidentes set asignado='$request->usuario' WHERE asignado='$previo'");
			DB::statement("UPDATE historial_periodos set asignado='$request->usuario' WHERE asignado='$previo'");
		}

		if ($res)
		{
			return back()->withMessage('Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->withError('Hubo un error al guardar los cambios');
		}
	}

	public function destroy($id)
	{
		$usuario = User::find($id);
		if ($usuario->delete())
		{
			return back()->withMessage('Se eliminó el usuario seleccionado');
		}
		else
		{
			return back()->withError('Hubo un error al eliminar el usuario');
		}
	}

	public function habilitarUsuario($id)
	{
		$user = User::findOrFail($id);
		$user->activo = $user->activo? 0 : 1;
		$user->save();

		return response($user);
	}

	public function perfilUsuario($id)
	{
		abort_if(Auth::user()->idT != $id, 403);

		$usuario = User::findOrFail($id);

		return view('usuario-perfil', compact('usuario'));
	}

	public function modificarPerfil(Request $request, $id)
	{
		abort_if(Auth::user()->idT != $id, 403); 
				
		$usuario = User::findOrFail($id);

		$request->validate([
			'nombre' => 'required|max:50',
			/* 'clave' => 'required|max:10' */
		]);

		//dd($request->all());
		//dd($request->validated()); exit();
		
		$usuario->nombre = $request->nombre;
		$usuario->Mail = $request->email;
		$usuario->Password = $request->clave? $request->clave : $usuario->Password;
		$res = $usuario->save();

		if ($request->file('avatar'))
		{
			if (Storage::exists('profile/'.$id.'.png'))
				Storage::delete('profile/'.$id.'.png');

			if (Storage::exists('profile/'.$id.'.jpg'))
				Storage::delete('profile/'.$id.'.jpg');

			if (Storage::exists('profile/'.$id.'.webp'))
				Storage::delete('profile/'.$id.'.webp');

			$ext = $request->file('avatar')->extension();
			$ext = str_replace('jpeg', 'jpg', strtolower($ext));

			$request->file('avatar')->storeAs('profile', "$id.$ext");
		}

		if ($res)
		{
			return back()->withMessage('Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->withError('Hubo un error al guardar los cambios');
		}
	}

}