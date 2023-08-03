<?php

class GruposController extends Controller
{

	public function index()
	{
		$grupos = Grupo::withCount(['miembros', 'incidentes'])
		->orderBy('descripcion')
		->paginate(20);

		//dd($grupos); exit();

		return view('admin.grupos', compact('grupos'));
	}

	public function create()
	{
		$usuarios = User::where('tipo', 1)->where('activo', 1)->orderBy('Usuario')->get();

		return view('admin.grupo-crear', compact('usuarios'));
	}

	public function store(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:grupos,descripcion'
		]);

		$grupo = Grupo::create([
			'descripcion' => $request->descripcion,
			'email' => $request->email
		]);

		if ($request->users)
		{
			$users = []; 
			foreach ($request->users as $key => $val)
				$users[] = $val;
			
			$grupo->miembros()->sync($users);
		}

		if ($grupo)
		{
			return to_route('grupos.index')->with('message', 'Se guardó el grupo correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el grupo');
		}

	}

	public function edit($id)
	{
		$grupo = Grupo::with('miembros:idT,Usuario,nombre')->findOrFail($id);
		$usuarios = User::where('tipo', 1)->where('activo', 1)->orderBy('Usuario')->get();

		return view('admin.grupo-editar', compact('grupo', 'usuarios'));
	}

	public function update(Request $request, $id)
	{
		$grupo = Grupo::findOrFail($id);
		$grupo->descripcion = $request->descripcion;
		$grupo->email = $request->email;
		
		if ($request->users)
		{
			$users = array(); 
			foreach ($request->users as $key => $val)
				$users[] = $val;

			$grupo->miembros()->sync($users);
		}
		else
		{
			$grupo->miembros()->sync([]);
		}

		if ($grupo->save())
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
		$grupo = Grupo::findOrFail($id);

		//dd($cliente); exit();

		if ($grupo->delete())
		{
			return back()->with('message', 'Se eliminó el grupo seleccionado');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el grupo');
		}
	}

	/* public function habilitarGrupo($id)
	{
		$cliente = Grupo::find($id);
		$cliente->activo = $cliente->activo? 0 : 1;
		$cliente->save();

		return response($cliente);
	} */

}