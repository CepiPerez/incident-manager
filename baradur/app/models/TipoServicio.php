<?php

class TipoServicio extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'codigo';
    
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'tipo_servicio');
    }
}