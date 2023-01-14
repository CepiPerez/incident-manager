<?php

class ClientesController extends Controller
{
	public function index()
	{
		//$clientes = Cliente::orderBy('descripcion')->get();

		$clientes = Cliente::selectRaw('clientes.*, tipo_servicios.descripcion as servicio')
			->withCount('incidentes')
			->leftJoin('tipo_servicios', 'codigo', '=', 'tipo_servicio')
			->orderBy('descripcion')
			->paginate(20);

		//dd(Cliente::first()->getQuery()); exit();

		return view('admin.clientes', compact('clientes'));
	}

	public function create()
	{
		return view('admin.cliente-crear');
	}

	public function store(Request $request)
	{
		//dd($request->all()); exit();

		$request->validate([
			'descripcion' => 'required|max:50|unique:clientes,descripcion'
		]);

		$cliente = new Cliente;
		$cliente->descripcion = $request->descripcion;
		$cliente->activo = 1;
		$res = $cliente->save();

		if ($res)
		{
			return to_route('clientes.index')->with('message', 'Se guardó el cliente correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el cliente');
		}

	}

	public function edit($id)
	{
		$cliente = Cliente::findOrFail($id);
		$areas = Area::all();

		$areas_cliente = $cliente->areas->pluck('codigo')->toArray();

		$tipo_servicio = TipoServicio::all();

		return view('admin.cliente-editar', compact('cliente', 'areas', 'areas_cliente', 'tipo_servicio'));
	}

	public function update(Request $request, $id)
	{
		$cliente = Cliente::findOrFail($id);

		if ($request->areas)
		{
			$areas = array(); 
			foreach ($request->areas as $key => $val)
				$areas[] = $val;
			
			$cliente->areas()->sync($areas);
		}

		$cliente->tipo_servicio = $request->tipo_servicio;
		$cliente->descripcion = $request->descripcion;
		$res = $cliente->save();


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
		$cliente = Cliente::find($id);

		//dd($cliente); exit();

		if ($cliente->softDeletes())
		{
			return back()->with('message', 'Se eliminó el cliente seleccionado');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el cliente');
		}
	}

	public function habilitar($id)
	{
		$cliente = Cliente::findOrFail($id);
		$cliente->activo = $cliente->activo? 0 : 1;
		$cliente->save();

		return response($cliente);
	}

}