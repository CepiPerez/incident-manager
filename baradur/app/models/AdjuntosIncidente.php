<?php

class AdjuntosIncidente extends Model
{

    protected $primaryKey = 'incidente';

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