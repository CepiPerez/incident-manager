<?php

class Avance extends Model
{
    public $timestamps = false;

    protected $fillable = ['incidente', 'tipo_avance', 'usuario', 'descripcion', 
        'fecha_ingreso', 'destino', 'grupo_destino', 'status_prev', 'asignado_prev', 'grupo_prev'];

    protected function id(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

    protected function fechaIngreso() : Attribute
    {
        return new Attribute(
            get: fn ($value) => date( "d-m-Y H:i", strtotime($value))
        );
    }

    protected function adjuntos() : Attribute
    {
        return new Attribute (
            get: fn ($value) => AdjuntosIncidente::where('avance', $this->id)
                                ->where('incidente', $this->incidente)->get()
        );

    }
    
}