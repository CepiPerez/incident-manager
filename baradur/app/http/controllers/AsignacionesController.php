<?php

class AsignacionesController extends Controller
{

    public function index()
    {
        $this->authorize('isadmin');

        $reglas = Asignacion::selectRaw('asignaciones.*, grupos.descripcion as grupo_nombre, 
			usuarios.nombre as usuario_nombre')
            ->leftJoin('grupos', 'codigo', '=', 'grupo')
            ->leftJoin('usuarios', 'Usuario', '=', 'usuario')
            ->orderBy('descripcion')->get();
    
        return view('admin.asignaciones', compact('reglas'));
    }

    public function create()
	{
        $this->authorize('isadmin');

        $grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();
		$clientes = Cliente::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();
		$areas = Area::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();
		$modulos = Modulo::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();
		$tipo_incidentes = TipoIncidente::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();

		return view('admin.asignacion-crear', compact('grupos', 'clientes', 'areas', 'modulos', 'tipo_incidentes'));
	}

    public function store(Request $request)
	{
		$this->authorize('isadmin');

		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:150',
			'conditions' => 'required'
		]);

		$regla = array();
		$regla['descripcion'] = $request->descripcion;
		$regla['grupo'] = $request->grupo;
		$regla['usuario'] = $request->asignado!="null"? $request->asignado : null;
		$regla['activo'] = 1; 
		$res = Asignacion::create($regla);

		AsignacionesCondicion::where('regla', $res->id)->delete();

		for ($i=0; $i < count($request->conditions); ++$i)
		{
			$rc = new AsignacionesCondicion();
			$rc->regla = $res->id;
			$rc->condicion = $request->conditions[$i];
			$rc->valor = $request->values[$i];
			$rc->helper = $request->text[$i];
			$rc->save();
		}

		if ($res)
		{
			return to_route('asignaciones.index')->with('message', 'Se guardó la regla correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar la regla');
		}

	}

    public function edit($id)
	{
        $this->authorize('isadmin');

		$regla = Asignacion::with('condiciones')->findOrFail($id);

        $grupos = Grupo::with('miembros:Usuario,nombre')->orderBy('descripcion')->get()->toArray();
		$clientes = Cliente::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();
		$areas = Area::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();
		$modulos = Modulo::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();
		$tipo_incidentes = TipoIncidente::orderBy('descripcion')->get()->pluck('descripcion', 'codigo')->toArray();

		return view('admin.asignacion-editar', compact('regla', 'grupos', 'clientes', 
			'areas', 'modulos', 'tipo_incidentes'));

	}

    public function update(Request $request, $id)
	{

		$regla = Asignacion::findOrFail($id);

		$request->validate([
			'descripcion' => 'required|max:150|unique:reglas,descripcion,'.$regla->descripcion,
			'conditions' => 'required'
		]);

		$regla->descripcion = $request->descripcion;
		$regla->grupo = $request->grupo;
		$regla->usuario = $request->asignado!="null"? $request->asignado : null;
		$res = $regla->save();

		AsignacionesCondicion::where('regla', $id)->delete();

		for ($i=0; $i < count($request->conditions); ++$i)
		{
			$rc = new AsignacionesCondicion();
			$rc->regla = $regla->id;
			$rc->condicion = $request->conditions[$i];
			$rc->valor = $request->values[$i];
			$rc->helper = $request->text[$i];
			$rc->save();
		}
			
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
		$regla = Asignacion::findOrFail($id);
		$res = $regla->delete();

		if ($res) {
			$res = AsignacionesCondicion::where('regla', $id)->delete();
		}

		if ($res)
		{
			return back()->withMessage('Se eliminó la regla seleccionado');
		}
		else
		{
			return back()->withError('Hubo un error al eliminar la regla');
		}
	}

	public function habilitar($id)
	{
		$regla = Asignacion::findOrFail($id);
		$regla->activo = $regla->activo? 0 : 1;
		$regla->save();

		return response($regla);
	}

	
}
