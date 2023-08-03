<?php

class AdminPolicy
{
    public function isAdmin($user)
    {
        return $user->rol==1;
    }

    public function crearInc($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return ( in_array(1, $perm) );
    }

    public function cargaMasiva($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(5, $perm);
    }

    public function tableroControl($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(6, $perm);
    }

    public function informes($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(7, $perm);
    }

    public function periodos($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(50, $perm);
    }

    public function crearPeriodos($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(51, $perm);
    }

    public function adminTareas($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(52, $perm);
    }

    public function incBacklog($user)
    {
        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(53, $perm);
    }

    public function adminPanel($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        
        foreach ($perm as $p) {
            if ($p > 100) return true;
        }

        return false;
    }

    public function adminUsuarios($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return ( in_array(101, $perm) || in_array(102, $perm) );
    }

    public function adminUsuariosInternos($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return ( in_array(101, $perm) );
    }

    public function adminRoles($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(103, $perm);
    }

    public function adminClientes($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(104, $perm);
    }

    public function adminAreas($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(105, $perm);
    }

    public function adminModulos($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(106, $perm);
    }

    public function adminTipoIncidente($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(107, $perm);
    }

    public function adminTipoServicio($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(108, $perm);
    }

    public function adminTipoAvance($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return in_array(109, $perm);
    }


    public function adminGrupos($user)
    {
        if ($user->rol==1) return true;
        if ($user->tipo==0) return false;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();
        return ( in_array(110, $perm));
    }

    public function verIncidente($user, $inc)
    {
        //dd($user); dd($inc); exit();

        if ($user->rol==1) return true;

        $rol = $user->roles;
        $perm = $rol? $rol->permisos->pluck('id')->toArray() : array();

        if (in_array(2, $perm))
            return true;

        if (in_array(3, $perm) && ($inc->usuario==$user->Usuario || $inc->remitente==$user->Usuario  || $inc->asignado==$user->Usuario) )
            return true;

        if (in_array(4, $perm) && ($inc->cliente->codigo==$user->cliente) )
            return true;

        return false;

    }

}