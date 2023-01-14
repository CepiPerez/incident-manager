<?php

Class Correo
{
    public static function verificar($avance)
    {
        //$avance = Avance::find($avance);
        //dd($avance);

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
            return self::enviarCorreo($avance, $cliente);
        }
        
        return false;

    }

    private static function enviarCorreo($avance, $cliente)
    {
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
        $template = str_replace('$LINK', route('incidentes.edit', $avance->incidente), $template);

        $subject = 'Aviso de actualizacion del incidente '.str_pad($avance->incidente, 7, '0', STR_PAD_LEFT);
        
        /* // PRUEBA mail()
        $encoding = "utf-8";
        $subject_preferences = array(
            "input-charset" => $encoding,
            "output-charset" => $encoding,
            "line-length" => 76,
            "line-break-chars" => "\r\n"
        );

        $header = "MIME-Version: 1.0 \r\n";
        $header .= "Content-type: text/html; charset=utf-8 \r\n";
        $header .= "Content-Transfer-Encoding: 8bit \r\n";
        $header .= "Date: ".date("r (T)")." \r\n";
        $header .= "From: SOPORTE NEWROL <soporte.newrol@gmail.com> \r\n";
        $header .= 'To: '.$cliente->Mail."\r\n";
        $header .= iconv_mime_encode("Subject", $subject, $subject_preferences);
        return mail($cliente->Mail, $subject, $template, $header); */

        Mail::to('cepiperez@gmail.com')
			->subject($subject)
			->send(new Notificacion($template));
		


    }


}