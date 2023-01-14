<?php

class AdjuntosIncidente extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'avance';

    protected function incidente(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

    protected function avance(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

}