<?php

class Cliente extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $primaryKey = 'codigo';

    public function scopeActivos($query)
    {
        return $query->/* with('areas')-> */where('activo', 1)->orderBy('descripcion');
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class, 'cliente');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'cliente', 'codigo')->orderBy('nombre');
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'codigo');
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