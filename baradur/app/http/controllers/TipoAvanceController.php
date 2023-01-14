<?php

class TipoAvanceController extends Controller
{
	public function index()
	{
		//$tipo_incidente = TipoIncidente::orderBy('descripcion')->get();	

		$tipo_avance = TipoAvance::orderBy('descripcion')->paginate(20);

		return view('admin.tipoavance', compact('tipo_avance'));
	}

	public function edit($id)
	{
		$tipo_avance = TipoAvance::findOrFail($id);
		
		$template = null;
		if (Storage::exists('../templates_correo/tipo_avance_'.(int)$id.'.html'))
			$template = Storage::get('../templates_correo/tipo_avance_'.(int)$id.'.html');

		return view('admin.tipoavance-editar', compact('tipo_avance', 'template'));
	}

	public function update(Request $request, $id)
	{
		$tipo_avance = TipoAvance::findOrFail($id);
		$tipo_avance->descripcion = $request->descripcion;
		$tipo_avance->visible = $request->visible=='on'? 1 : 0;
		$tipo_avance->correo = $request->correo=='on'? 1 : 0;

		$res = $tipo_avance->save();

		if ($res)
		{
			Storage::put('../templates_correo/tipo_avance_'.(int)$id.'.html', $request->html);

			return back()->with('message', 'Se guardaron los cambios correctamente');
		}
		else
		{
			return back()->with('error', 'Hubo un error al guardar los cambios');
		}
	}

}