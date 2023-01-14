<?php

class Grupo extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'codigo';

    protected $fillable = [
        'descripcion'
    ];

    public function miembros()
    {
        return $this->belongsToMany(User::class, 'idT', 'codigo');
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class, 'grupo', 'codigo');
    }


}