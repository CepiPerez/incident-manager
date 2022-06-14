<?php

class User extends Model
{
    protected $table = 'usuarios';

    protected $primaryKey = 'idT';

    public static function activos()
    {
        return User::where('activo', 1)->orderBy('Usuario')->get();
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class, 'usuario', 'Usuario');
    }

    public function asignados()
    {
        return $this->hasMany(Incidente::class, 'asignado', 'Usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo', 'cliente');
    }

    protected function nombre(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value?? $this->Usuario
        );
    }
    
    protected function idT(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

}

