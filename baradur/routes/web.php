<?php

# Default Home controller

Auth::routes(['register' => false, 'reset'=> false]);

Route::controller(IncidentesController::class)->middleware('auth')->group( function()
{
    Route::get('', 'inicio')->name('incidentes');
    Route::get('incidente/{$id}', 'mostrar')->name('incidente.mostrar');
    Route::get('incidente/crear', 'crear')->name('incidente.crear');
    Route::post('incidente', 'guardar')->name('incidente.guardar');
    Route::post('incidente/{id}/avance', 'guardarAvance')->name('incidente.avance.guardar');
    Route::post('incidente/{id}/nota', 'guardarNota')->name('incidente.nota.guardar');
    Route::get('incidente/descargar/{incidente}/{avance}/{adjunto}', 'descargarAdjunto')->name('incidente.descargar.adjunto');
    Route::get('incidente/{$id}/editar', 'editar')->name('incidente.editar');
    Route::put('incidente/{$id}', 'modificar')->name('incidente.modificar');
    Route::get('cargamasiva', 'cargaMasiva')->name('cargamasiva');
    Route::get('cargamasiva/descargar_plantilla', 'descargarExcel')->name('cargamasiva.descargarexcel');
    Route::post('cargamasiva', 'procesar')->name('cargamasiva.guardar');
    Route::get('informes', 'informes')->name('informes');
    Route::get('informes/procesar', 'procesarInforme')->name('informes.procesar');
    Route::get('informes/descargar', 'descargarInforme')->name('informes.descargar');
});

Route::controller(UsuariosController::class)->middleware('auth')->group( function()
{
    Route::get('usuarios', 'usuarios')->name('usuarios');
    Route::get('usuarios/crear', 'crearUsuario')->name('usuarios.crear');
    Route::post('usuarios', 'guardarUsuario')->name('usuarios.guardar');
    Route::delete('usuarios/{id}', 'eliminarUsuario')->name('usuarios.eliminar');
    Route::get('usuarios/{id}/editar', 'editarUsuario')->name('usuarios.editar');
    Route::put('usuarios/{id}', 'modificarUsuario')->name('usuarios.modificar');
    Route::get('usuarios/{id}/habilitar', 'habilitarUsuario')->name('usuarios.habilitar');
});


Route::controller(ClientesController::class)->middleware('auth')->group( function()
{
    Route::get('clientes', 'clientes')->name('clientes');
    Route::get('clientes/crear', 'crearCliente')->name('clientes.crear');
    Route::post('clientes', 'guardarCliente')->name('clientes.guardar');
    Route::delete('clientes/{id}', 'eliminarCliente')->name('clientes.eliminar');
    Route::get('clientes/{id}/editar', 'editarCliente')->name('clientes.editar');
    Route::put('clientes/{id}', 'modificarCliente')->name('clientes.modificar');
    Route::get('clientes/{id}/habilitar', 'habilitarCliente')->name('clientes.habilitar');

});

Route::controller(TipoIncidenteController::class)->middleware('auth')->group( function()
{
    Route::get('tipoincidente', 'index')->name('tipoincidente');
    Route::get('tipoincidente/crear', 'crearTipoIncidente')->name('tipoincidente.crear');
    Route::post('tipoincidente', 'guardarTipoIncidente')->name('tipoincidente.guardar');
    Route::delete('tipoincidente/{id}', 'eliminarTipoIncidente')->name('tipoincidente.eliminar');
    Route::get('tipoincidente/{id}/editar', 'editarTipoIncidente')->name('tipoincidente.editar');
    Route::put('tipoincidente/{id}', 'modificarTipoIncidente')->name('tipoincidente.modificar');
    Route::get('tipoincidente/{id}/habilitar', 'habilitarTipoIncidente')->name('tipoincidente.habilitar');
});

Route::controller(ModulosController::class)->middleware('auth')->group( function()
{
    Route::get('modulos', 'index')->name('modulos');
    Route::get('modulos/crear', 'crearModulo')->name('modulos.crear');
    Route::post('modulos', 'guardarModulo')->name('modulos.guardar');
    Route::delete('modulos/{id}', 'eliminarModulo')->name('modulos.eliminar');
    Route::get('modulos/{id}/editar', 'editarModulo')->name('modulos.editar');
    Route::put('modulos/{id}', 'modificarModulo')->name('modulos.modificar');
    Route::get('modulos/{id}/habilitar', 'habilitarModulo')->name('modulos.habilitar');
});

Route::controller(AreasController::class)->middleware('auth')->group( function()
{
    Route::get('areas', 'index')->name('areas');
    Route::get('areas/crear', 'crearArea')->name('areas.crear');
    Route::post('areas', 'guardarArea')->name('areas.guardar');
    Route::delete('areas/{id}', 'eliminarArea')->name('areas.eliminar');
    Route::get('areas/{id}/editar', 'editarArea')->name('areas.editar');
    Route::put('areas/{id}', 'modificarArea')->name('areas.modificar');
});

Route::controller(TipoServicioController::class)->middleware('auth')->group( function()
{
    Route::get('tiposervicio', 'index')->name('tiposervicio');
    Route::get('tiposervicio/crear', 'crearTipoServicio')->name('tiposervicio.crear');
    Route::post('tiposervicio', 'guardarTipoServicio')->name('tiposervicio.guardar');
    Route::delete('tiposervicio/{id}', 'eliminarTipoServicio')->name('tiposervicio.eliminar');
    Route::get('tiposervicio/{id}/editar', 'editarTipoServicio')->name('tiposervicio.editar');
    Route::put('tiposervicio/{id}', 'modificarTipoServicio')->name('tiposervicio.modificar');
    Route::get('tiposervicio/{id}/habilitar', 'habilitarTipoServicio')->name('tiposervicio.habilitar');
});




