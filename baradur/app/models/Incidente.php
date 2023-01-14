<?php

class Incidente extends Model
{
    public $timestamps = false;

    protected $casts = [
        'fecha_ingreso' => 'date'
    ];

    protected $fillable = [
        'cliente', 'titulo', 'descripcion', 'mail', 'tel', 'fecha_ingreso', 'tipo_incidente', 
        'status', 'grupo', 'asignado', 'usuario', 'remitente', 'area', 'modulo', 'prioridad', 'sla'
    ];

    public function inc_cliente()
    {
        return $this->hasOne(Cliente::class, 'codigo', 'cliente');
    }

    public function tipo_incidente()
    {
        return $this->hasOne(TipoIncidente::class, 'codigo', 'tipo_incidente');
    }

    public function estado()
    {
        return $this->hasOne(StatusIncidente::class, 'codigo', 'status');
    }

    public function avances()
    {
        /* if(Auth::user()->cliente==5)
            return $this->hasMany(Avance::class, 'incidente', 'id')
                ->selectRaw('avances.*, ta.descripcion as tipo_desc, gr.descripcion as grupo_desc')
                ->leftJoin('tipo_avances as ta', 'codigo', '=', 'tipo_avance')
                ->leftJoin('grupos as gr', 'codigo', '=', 'grupo_destino');
        else */
            return $this->hasMany(Avance::class, 'incidente', 'id')
                ->selectRaw('avances.*, ta.descripcion as tipo_desc, ta.visible, gr.descripcion as grupo_desc')
                ->leftJoin('tipo_avances as ta', 'codigo', '=', 'tipo_avance')
                ->leftJoin('grupos as gr', 'codigo', '=', 'grupo_destino')
                ->where('tipo_avance', '<', 101)->orderBy('fecha_ingreso');

    }

    public function avances_resumido()
    {
        return $this->hasMany(Avance::class, 'incidente', 'id')
                ->selectRaw('incidente, fecha_ingreso, tipo_avance')
                ->where('tipo_avance', '<', 30)
                ->orderBy('fecha_ingreso');
    }

    public function inc_usuario()
    {
        return $this->hasOne(User::class, 'Usuario', 'usuario');
    }

    public function inc_asignado()
    {
        return $this->hasOne(User::class, 'Usuario', 'asignado');
    }

    /* protected function id() : Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    } */


    protected function fecha() : Attribute
    {
        return new Attribute(
            get: fn ($value) => date( "d-m-Y H:i", strtotime($this->fecha_ingreso))
        );
    }

    /* protected function descripcion(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    } */

    /* public function getShortAttribute()
    {
        if (strpos($this->descripcion, "\n")!==false)
            return substr($this->descripcion, 0, strpos($this->descripcion, "\n") -1);
        else
            return $this->descripcion;
    } */

    /* public function getLargeAttribute()
    {
        if (strpos($this->descripcion, "\n")!==false)
        {
            $short = substr($this->descripcion, 0, strpos($this->descripcion, "\n"));
            return preg_replace("/\r\n|\r|\n/", ' ', str_replace($short."\n", '', $this->descripcion));
        }
        else
            return $this->descripcion;
    } */


}