<?php

class Cliente extends Model
{

    protected $primaryKey = 'codigo';

    public static function activos()
    {
        return Cliente::with('areas')->where('activo', 1)->orderBy('descripcion')->get();
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'codigo', 'codigo');
    }

    public function servicio()
    {
        return $this->hasOne(TipoServicio::class, 'codigo', 'tipo_servicio');
    }

    protected function codigo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => (int)$value
        );
    }

}