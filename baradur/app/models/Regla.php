<?php

class Regla extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'descripcion', 'pondera', 'activo'
    ];

    public function condiciones()
    {
        return $this->hasMany(ReglasCondicion::class, 'regla', 'id');
    }
    
}