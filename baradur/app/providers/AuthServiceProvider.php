<?php

class AuthServiceProvider extends ServiceProvider
{


    public function boot()
    {
        Gate::define('isadmin', 'AdminPolicy@isAdmin');
        Gate::define('crear_inc', 'AdminPolicy@crearInc');
        Gate::define('carga_masiva', 'AdminPolicy@cargaMasiva');
        Gate::define('tablero_control', 'AdminPolicy@tableroControl');
        Gate::define('informes', 'AdminPolicy@informes');
        Gate::define('periodos', 'AdminPolicy@periodos');
        Gate::define('crear_periodos', 'AdminPolicy@crearPeriodos');
        Gate::define('admin_tareas', 'AdminPolicy@adminTareas');
        Gate::define('inc_backlog', 'AdminPolicy@incBacklog');

        Gate::define('admin_panel', 'AdminPolicy@adminPanel');
        Gate::define('admin_usuarios', 'AdminPolicy@adminUsuarios');
        Gate::define('admin_internos', 'AdminPolicy@adminUsuariosInternos');
        Gate::define('admin_grupos', 'AdminPolicy@adminGrupos');
        Gate::define('admin_clientes', 'AdminPolicy@adminClientes');
        Gate::define('admin_roles', 'AdminPolicy@adminRoles');
        Gate::define('admin_areas', 'AdminPolicy@adminAreas');
        Gate::define('admin_modulos', 'AdminPolicy@adminModulos');
        Gate::define('admin_tipoincidente', 'AdminPolicy@adminTipoIncidente');
        Gate::define('admin_tiposervicio', 'AdminPolicy@adminTipoServicio');
        Gate::define('admin_tipoavance', 'AdminPolicy@adminTipoAvance');

        Gate::define('ver_inc', 'AdminPolicy@verIncidente');

    }

}
