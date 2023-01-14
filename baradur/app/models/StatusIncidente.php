<?php

class StatusIncidente extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'codigo';

    protected function codigo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }


}