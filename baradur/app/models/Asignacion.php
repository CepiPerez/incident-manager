<?php

class Asignacion extends Model
{    
    public $timestamps = false;

    protected $fillable = ['descripcion', 'grupo', 'usuario', 'activo'];

    public function condiciones()
    {
        return $this->hasMany(AsignacionesCondicion::class, 'regla');
    }

}
