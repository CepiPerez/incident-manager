<?php

class CargaMasivaController extends Controller
{

	public function cargaMasiva()
	{
		return view('masiva');
	}

	public function procesar(Request $request)
	{
		# Guardamos el archivo a procesar
		$name =  strtolower($request->file('archivo')->name());
        $extension =  strtolower($request->file('archivo')->extension());
        $newfile = Storage::path('archivos').'/'.$name;

        if (!$request->file('archivo')->isValid())
        {
            return back()->with('error', 'Verifique el archivo a procesar');
        }

        $request->file('archivo')->storeAs('archivos', $name);


		# Cargamos el archivo en un array
        $datos = null;
        
        if ($extension == 'xls')
        {
            if ( $xls = SimpleXLS::parse($newfile) ) {
                $datos = $xls->rows();
            } else {
                echo SimpleXLS::parseError();
            }
        }
        elseif ($extension == 'xlsx')
        {
            if ( $xls = SimpleXLSX::parse($newfile) ) {
                $datos = $xls->rows();
            } else {
                echo SimpleXLSX::parseError();
            }
        }
        else
        {
            return back()->with('error', 'No se puede procesar el archivo');
        }


		# Procesamos el archivo usando la cabecera como KEY
		$final = array();
		$cabeceras = array_shift($datos);

		for ($i=0; $i<count($datos); ++$i)
		{
			$linea = array();
			for ($k=0; $k<count($cabeceras); ++$k)
			{
				$cabecera = trim(strtolower($cabeceras[$k]));
				$linea[$cabecera] = (is_string($datos[$i][$k]) && $datos[$i][$k]=='')? null: $datos[$i][$k];
			}
			$final[] = $linea;
		}

		
		//dd($final); 

		# Subimos los datos a MySQL

		$result = true;

		foreach ($final as $dato)
		{

			$date = strtotime(substr($dato['fecha'], 0, 10).' '.substr($dato['hora'], -8));

			$cliente = Cliente::where('descripcion', $dato['cliente'])->first();

			$inc = new Incidente;
			$inc->cliente = (int)$cliente->codigo;
			$inc->area = (int)$cliente->areas()->first()->codigo;
			$inc->modulo = 65;
			$inc->programa = 0;
			$inc->tipo_incidente = 16;
			$inc->titulo = $dato['titulo'];
			$inc->descripcion = $dato['descripcion'];
			$inc->menu = '';
			$inc->usuario = Auth::user()->Usuario;
			$inc->asignado = Auth::user()->Usuario;
			$inc->punto_menu = 0;
			$inc->mail = '';
			$inc->tel = '';
			$inc->status = 10;
			$inc->fecha_ingreso = date('Y-m-d H:i', $date);
			$inc->prioridad = 40;

			//dd($inc); exit();
			if (!$inc->save())
				$result = false;
		}

		if ($result)
			return back()->with('message', 'Se guardaron los registros correctamente');
		else
			return back()->with('error', 'Hubo un error al guardar los registros');

	}

	public function descargarExcel()
	{
		Storage::download("plantilla_incidentes.xlsx");
	}


}