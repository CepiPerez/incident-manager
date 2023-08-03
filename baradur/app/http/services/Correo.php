<?php

Class Correo
{
    public static function verificar($avance)
    {
        //$avance = Avance::find($avance);
        //dd($avance, in_array((int)$avance->tipo_avance, array(2, 101)));


        // Por ahora solamente enviamos mail para:
        // 101 - Creacion de incidente
        // 2 - Derivacion de incidente
        if (!in_array((int)$avance->tipo_avance, array(2, 101))) {
            return;
        }

        self::envioInterno($avance);

        //self::envioAlCliente($avance);
    }

    private static function envioInterno($avance)
    {
        $usuario = User::where('Usuario', $avance->destino)->first();
        $email = $usuario->Mail;
        $enviar = TipoAvance::find($avance->tipo_avance)->correo==1;
        
        $domain = ltrim(stristr($email, '@'), '@');
        $user = str_replace('@'.$domain, '', $email);
        
        if (!empty($user) && !empty($domain) && $enviar ) //&& $cliente->cliente!=5)
        {
            //Enviar notificacion por correo
            return self::enviarCorreo($avance, $email);
        }
        
        return false;
    }

    private static function envioAlCliente($avance)
    {
        $usuario = Incidente::find($avance->incidente)->usuario;
        $cliente = User::where('Usuario', $usuario)->first();
        $email = $cliente->Mail;
        $enviar = TipoAvance::find($avance->tipo_avance)->correo==1;
        
        $domain = ltrim(stristr($email, '@'), '@');
        $user = str_replace('@'.$domain, '', $email);
        //var_dump($user); var_dump($domain);

        if (!empty($user) && !empty($domain) && $enviar ) //&& $cliente->cliente!=5)
        {
            //Enviar notificacion por correo
            return self::enviarCorreo($avance, $email);
        }
        
        return false;
    }


    private static function enviarCorreo($avance, $destino)
    {
        //dd($avance, $destino);
        //echo "Enviando correo a $cliente->Mail...";

        if (!Storage::exists('../templates_correo/tipo_avance_'.$avance->tipo_avance.'.html'))
            return;

        if ($avance->destino)
            $dest = User::where('Usuario', $avance->destino)->first()->nombre;
        else
            $dest = null;

        if (strlen($avance->descripcion)>1)
            $desc = 'Detalle de la actualizacion: <br><b>' . $avance->descripcion . '</b><br>';
        else
            $desc = null;

        $template = Storage::get('../templates_correo/tipo_avance_'.$avance->tipo_avance.'.html');
        $template = str_replace('$INCIDENTE', str_pad($avance->incidente, 7, '0', STR_PAD_LEFT), $template);
        $template = str_replace('$REPRESENTANTE_NUEVO', $dest, $template);
        $template = str_replace('$REPRESENTANTE', Auth::user()->nombre, $template);
        $template = str_replace('$DESCRIPCION', $desc, $template);
        $template = str_replace('$LINK', route('incidentes.show', $avance->incidente), $template);
        $template = str_replace('$LOGO', asset('assets/logonewrol.png'), $template);

        $subject = 'Aviso de actualizacion del incidente '.str_pad($avance->incidente, 7, '0', STR_PAD_LEFT);

        if ((int)$avance->tipo_avance==101) {
            $subject = 'Aviso de creación del incidente '.str_pad($avance->incidente, 7, '0', STR_PAD_LEFT);
        }

        $body = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Document</title>
            </head>
            <body>
                ' . $template . '
            </body>
            </html>';

        Mail::to($destino)
			->subject($subject)
			->send($body);
		
    }


}