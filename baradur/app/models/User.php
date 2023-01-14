<?php

class User extends Model
{
    public $timestamps = false;

    protected $table = 'usuarios';

    protected $primaryKey = 'idT';

    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('nombre');
    }

    public function creados()
    {
        return $this->hasMany(Incidente::class, 'usuario', 'Usuario');
    }

    public function asignados()
    {
        return $this->hasMany(Incidente::class, 'asignado', 'Usuario');
    }

    public function remitente()
    {
        return $this->hasMany(Incidente::class, 'remitente', 'Usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo', 'cliente');
    }

    public function roles()
    {
        return $this->hasOne(Rol::class, 'id', 'rol');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'codigo', 'idT', 'user_codigo', 'grupo_codigo');
    }

    public function getAvatarAttribute()
    {
        if (Storage::exists('profile/'. (int)$this->idT.'.png'))
            return Storage::url('profile/'. (int)$this->idT.'.png');
            
        if (Storage::exists('profile/'. (int)$this->idT.'.jpg'))
            return Storage::url('profile/'. (int)$this->idT.'.jpg');

        if (Storage::exists('profile/'. (int)$this->idT.'.webp'))
            return Storage::url('profile/'. (int)$this->idT.'.webp');
        
        //$nombre = str_replace(' ', '+', $this->nombre ?? $this->Usuario);
        //$avatar = Http::get("https://ui-avatars.com/api/?name=$nombre&background=random", false);
        //Storage::put('profile/'.(int)$this->idT.'.png', $avatar);
        //return Storage::url('/public/profile/'. (int)$this->idT.'.png');

        return Storage::url('profile/default.png');
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

