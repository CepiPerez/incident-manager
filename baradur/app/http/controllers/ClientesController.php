<?php

class ClientesController extends Controller
{

	public function clientes()
	{
		//$clientes = Cliente::orderBy('descripcion')->get();

		$clientes = Cliente::selectRaw('clientes.*, tipo_servicios.descripcion as servicio, COALESCE(i.cnt,0) AS contador')
		->joinSub('SELECT cliente, count(cliente) cnt FROM incidentes GROUP BY cliente', 'i',
			'i.cliente', '=', 'clientes.codigo', 'LEFT')
		->join('tipo_servicios', 'codigo', '=', 'tipo_servicio')
		->orderBy('descripcion')
		->get();

		//dd($clientes);

		return view('admin.clientes', compact('clientes'));
	}

	public function eliminarCliente($id)
	{
		$cliente = Cliente::find($id);

		//dd($cliente); exit();

		if ($cliente->delete())
		{
			return back()->with('message', 'Se eliminó el cliente seleccionado');
		}
		else
		{
			return back()->with('error', 'Hubo un error al eliminar el cliente');
		}
	}

	public function crearCliente()
	{
		return view('admin.cliente-crear');
	}

	public function guardarCliente(Request $request)
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
			return back()->with('message', 'Se guardó el cliente correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar el cliente');
		}

	}

	public function editarCliente($id)
	{
		$cliente = Cliente::find($id);
		$areas = Area::get();
		$areas_cliente = $cliente->areas->pluck('codigo')->toArray();
		$tipo_servicio = TipoServicio::get();

		return view('admin.cliente-editar', compact('cliente', 'areas', 'areas_cliente', 'tipo_servicio'));
	}

	public function modificarCliente(Request $request, $id)
	{
		$cliente = Cliente::find($id);

		if ($request->areas)
		{
			$areas = array(); 
			foreach ($request->areas as $key => $val)
				$areas[] = $val;
			
			$cliente->areas()->sync($areas);
		}

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

	public function habilitarCliente($id)
	{
		$cliente = Cliente::find($id);
		$cliente->activo = $cliente->activo? 0 : 1;
		$cliente->save();

		return response($cliente);
	}

}