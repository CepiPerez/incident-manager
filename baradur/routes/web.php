<?php

# Default Home controller

Auth::routes(['register' => false, 'reset'=> false]);

Route::redirect('/', 'incidentes');

Route::middleware('auth')->group( function() {

    Route::get('tareas', [IncidentesController::class, 'tareas'])->name('tareas');

    Route::resource('incidentes', IncidentesController::class)->except(['edit', 'delete']);

    Route::controller(IncidentesController::class)->group( function()
    {
        Route::post('incidentes/{id}/nota', 'guardarNota')->name('incidente.nota.guardar');
        Route::get('incidentes/descargar/{incidente}/{avance}/{adjunto}', 'descargarAdjunto')->name('incidente.descargar.adjunto');
        Route::post('incidentes/{id}/avance', 'guardarAvance')->name('incidente.avance.guardar');
        Route::delete('incidentes/{id}/avance/{avance}', 'eliminarAvance')->name('incidente.avance.eliminar');
    });

    Route::controller(InformesController::class)->group( function()
    {
        Route::get('informes', 'informes')->name('informes');
        Route::get('informes/procesar', 'procesarInforme')->name('informes.procesar');
        Route::get('informes/descargar', 'descargarInforme')->name('informes.descargar');
    });

    Route::get('tablero/{param?}', [TableroController::class, 'tablero'])->name('dashboard');

    Route::get('usuarios/{id}/perfil', [UsuariosController::class, 'perfilUsuario'])->name('usuarios.perfil');
    Route::put('usuarios/{id}/perfil', [UsuariosController::class, 'modificarPerfil'])->name('usuarios.perfil.modificar');

});

Route::middleware(['auth', 'interno'])->group( function() {

    Route::resource('periodos', PeriodoController::class);
    Route::get('periodos/vencidos', [PeriodoController::class, 'incidentesVencidos'])->name('periodos.vencidos');
    Route::get('periodos/{id}/{filtro}', [PeriodoController::class, 'filtrarIncidentes'])->name('periodos.filtrar');
    Route::post('periodos/mover', [PeriodoController::class, 'moverIncidentes'])->name('periodos.mover');

    Route::resource('usuarios', UsuariosController::class)->except(['show']);
    Route::get('usuarios/{id}/habilitar', [UsuariosController::class, 'habilitarUsuario'])->name('usuarios.habilitar');
    
    Route::resource('grupos', GruposController::class)->except(['show']);

    Route::resource('areas', AreasController::class)->except(['show']);

    Route::resource('roles', RolesController::class)->except(['show']);
    
    Route::resource('tiposervicios', TipoServicioController::class)->except(['show']);
    
    Route::resource('tipoavances', TipoAvanceController::class)->only(['index', 'edit', 'update']);
    
    Route::resource('prioridades', PrioridadesController::class)->only(['index', 'edit', 'update']);

    Route::resource('clientes', ClientesController::class)->except(['show']);
    Route::get('clientes/{id}/habilitar', [ClientesController::class, 'habilitar'])->name('clientes.habilitar');

    Route::resource('modulos', ModulosController::class)->except(['show']);
    Route::get('modulos/{id}/habilitar', [ModulosController::class, 'habilitar'])->name('modulos.habilitar');

    Route::resource('reglas', ReglasPrioridadController::class)->except(['show']);
    Route::get('reglas/{id}/habilitar', [ReglasPrioridadController::class, 'habilitar'])->name('reglas.habilitar');
    
    Route::resource('tipoincidentes', TipoIncidenteController::class)->except(['show']);
    Route::get('tipoincidentes/{id}/habilitar', [TipoIncidenteController::class, 'habilitar'])->name('tipoincidentes.habilitar');
    
    Route::resource('asignaciones', AsignacionesController::class)->except(['show']);
    Route::get('asignaciones/{id}/habilitar', [AsignacionesController::class, 'habilitar'])->name('asignaciones.habilitar');
    
    Route::controller(CargaMasivaController::class)->group( function()
    {
        Route::get('cargamasiva', 'cargaMasiva')->name('cargamasiva');
        Route::post('cargamasiva', 'procesar')->name('cargamasiva.guardar');
        Route::get('cargamasiva/descargar_plantilla', 'descargarExcel')->name('cargamasiva.descargarexcel');
    });

    Route::controller(HerramientasController::class)->group( function()
    {
        Route::get('herramientas', 'herramientas')->name('herramientas');
        Route::get('herramientas/resolver', 'resolver')->name('herramientas.resolver');
        Route::get('herramientas/cerrar', 'cerrar')->name('herramientas.cerrar');

        Route::get('herramientas/buscar_incidente', 'buscarIncidente')->name('herramientas.buscar_incidente');
    });

});


Route::get('correo/{$avance}', function($avance) {
    Correo::verificar($avance);
});

Route::get('test/{name}', function($test) {
    $faker = new Faker();
    dump($faker->email());
    dd(User::where('Usuario', $test)->first());
});


