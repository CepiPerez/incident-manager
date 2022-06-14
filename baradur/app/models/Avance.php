<?php

class Avance extends Model
{

    protected $fillable = ['incidente', 'tipo_avance', 'usuario', 'descripcion', 'fecha_ingreso'];

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


    
}