<?php

class Incidente extends Model
{

    //protected $primaryKey = 'id';

    protected $fillable = [
        'cliente', 'descripcion', 'mail', 'tel', 'fecha_ingreso', 'tipo_incidente', 'status', 
        'asignado', 'usuario', 'area', 'modulo', 'prioridad'
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'codigo', 'cliente');
    }

    public function tipo_incidente()
    {
        return $this->hasOne(TipoIncidentes::class, 'codigo', 'tipo_incidente');
    }

    public function status()
    {
        return $this->hasOne(StatusIncidentes::class, 'codigo', 'status');
    }

    public function avances()
    {
        if(Auth::user()->cliente==5)
            return $this->hasMany(Avance::class, 'incidente', 'id');
        else
            return $this->hasMany(Avance::class, 'incidente', 'id')->where('tipo_avance', '<', 50);

    }

    protected function fecha() : Attribute
    {
        return new Attribute(
            get: fn ($value) => date( "d-m-Y H:i", strtotime($this->fecha_ingreso))
        );
    }

    protected function descripcion(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    public function getShortAttribute()
    {
        if (strpos($this->descripcion, "\n")!==false)
            return substr($this->descripcion, 0, strpos($this->descripcion, "\n") -1);
        else
            return $this->descripcion;
    }

    public function getLargeAttribute()
    {
        if (strpos($this->descripcion, "\n")!==false)
        {
            $short = substr($this->descripcion, 0, strpos($this->descripcion, "\n"));
            return preg_replace("/\r\n|\r|\n/", ' ', str_replace($short."\n", '', $this->descripcion));
        }
        else
            return $this->descripcion;
    }


}