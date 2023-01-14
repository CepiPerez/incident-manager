<?php

class Rol extends Model
{
    public $timestamps = false;

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'id', 'id');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol');
    }
    
}