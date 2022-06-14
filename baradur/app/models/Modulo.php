<?php

class Modulo extends Model
{

    protected $primaryKey = 'codigo';

    protected function codigo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }
}