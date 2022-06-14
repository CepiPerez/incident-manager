<?php

class Area extends Model
{

    protected $primaryKey = 'codigo';

    protected function codigo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

    protected function cliente(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

    
}