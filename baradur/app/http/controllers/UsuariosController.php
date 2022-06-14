<?php

class UsuariosController extends Controller
{

	public function usuarios()
	{
		$usuarios = User::with('cliente')
			->selectRaw('usuarios.*, COALESCE(i.cnt,0) AS creados, COALESCE(i2.cnt,0) AS asignados')
			->joinSub('SELECT usuario, count(usuario) cnt FROM incidentes GROUP BY usuario', 'i',
				'i.usuario', '=', 'usuarios.Usuario', 'LEFT')
			->joinSub('SELECT asignado, count(asignado) cnt FROM incidentes GROUP BY asignado', 'i2',
				'i2.asignado', '=', 'usuarios.Usuario', 'LEFT')
			->orderBy('Usuario')
			->get();

		//dd($usuarios);

		foreach ($usuarios as $user)
		{
			$user->contador = $user->creados + $user->asignados;
		}

		return view('admin.usuarios', compact('usuarios'));
	}

	public function eliminarUsuario($id)
	{
		$usuario = User::find($id);
		if ($usuario->delete())
		{
			return back()->with('message', 'Se eliminó el usuario seleccionado');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el usuario');
		}
	}

	public function crearUsuario()
	{
		$clientes = Cliente::activos();
		return view('admin.usuario-crear', compact('clientes'));
	}

	public function guardarUsuario(Request $request)
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
		$usuario->cliente = $request->cliente;
		$usuario->activo = 1;
		$usuario->nivel = 1;
		$usuario->status = 1;
		$res = $usuario->save();

		if ($res)
		{
			return back()->with('message', 'Se guardó el usuario correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el usuario');
		}

	}

	public function editarUsuario($id)
	{
		$usuario = User::find($id);
		$clientes = Cliente::activos();
		return view('admin.usuario-editar', compact('usuario', 'clientes'));
	}

	public function modificarUsuario(Request $request, $id)
	{
				
		$usuario = User::find($id);
		$usuario->nombre = $request->nombre;
		$usuario->Mail = $request->email;
		$usuario->cliente = $request->cliente;
		if ($request->clave!='') $usuario->Password = $request->clave;
		$usuario->rol = $request->rol;
		$res = $usuario->save();

		if ($res)
		{
			return back()->with('message', 'Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}
	}

	public function habilitarUsuario($id)
	{
		$user = User::find($id);
		$user->activo = $user->activo? 0 : 1;
		$user->save();

		return response($user);
	}

}