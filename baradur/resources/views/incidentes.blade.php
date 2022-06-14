@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-lg pt-2">Gestion de incidentes</h3>

        <div class="col-lg botonera pr-0 pl-3">

            @if ($sin_asignar>0 && (!isset($filtros['status']) || $filtros['status']!='sin_asignar') && Auth::user()->cliente=='5')
                <a href="#" onclick="sinAsignar()" class="col-auto btn btn-outline-danger btn-sm mt-0 mb-0 mr-2">
                Hay incidentes sin asignar</a>
            @endif
    
            @if ( isset($filtros['usuario']) || isset($filtros['cliente']) || isset($filtros['status']) 
                  || isset($filtros['modulo']) || isset($filtros['tipo_incidente']) || isset($filtros['prioridad']) )
                <button class="col-auto btn btn-warning btn-sm mt-2 mb-2 pl-3 pr-3" data-toggle="modal" data-target="#filtrarModal">
                Hay filtros aplicados
            @else
                <button class="col-auto btn btn-secondary btn-sm mt-2 mb-2 pl-3 pr-3" data-toggle="modal" data-target="#filtrarModal">
                Filtrar
            @endif
            </button>
            <a href="{{ route('incidente.crear') }}" class="col-auto btn btn-success btn-sm ml-2 mt-2 mb-2 pl-3 pr-3 text-white">
                Nuevo incidente</a>

        </div>
    </div>
    <hr class="mb-3 mt-0">


    <!-- Modal para filtrar incidentes -->
    <div class="modal fade" id="filtrarModal" tabindex="-1" role="dialog" 
        aria-labelledby="filtrarModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="min-width:60vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtrarModalLabel">Aplicar filtros</h5>
            </div>
            <div style="margin:15px;">
                <form action="{{ route('incidentes.filtrar') }}" method="get" id="filtrarIncidentes" class="mb-3">

                    @if (Auth::user()->cliente==5)
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="usuario">Usuario asignado</label>
                            <select class="form-control" id="usuario" name="usuario">
                                <option value="todos">Todos los usuarios</option>
                                @foreach ($usuarios as $key => $val)
                                <option value="{{$key}}" @selected($filtros['usuario']==$key)>{{$val}}</option>
                                @endforeach
                            </select>
                        </div>  
    
                        <div class="col-6 form-group">
                            <label for="cliente">Cliente</label>
                            <select class="form-control" id="cliente" name="cliente">
                                <option value="todos">Todos los clientes</option>
                                @foreach ($clientes as $cli)
                                <option value="{{$cli['codigo']}}" @selected($filtros['cliente']==$cli['codigo'])>
                                    {{$cli['descripcion']}}</option>
                                @endforeach
                            </select>
                        </div> 
                    </div>

                    <div class="row">
                        <div class="form-group col-6">
                            <label for="tipo_incidente">Tipo de incidente</label>
                            <select id="tipo_incidente" name="tipo_incidente" class="form-control">
                                <option value="todos">Todos los tipos</option>
                                @foreach ($tipo_incidente as $ti)
                                <option value="{{$ti->codigo}}" @selected($filtros['tipo_incidente']==$ti->codigo)>
                                    {{$ti->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="modulo">Modulo</label>
                            <select id="modulo" name="modulo" class="form-control">
                                <option value="todos">Todos los modulos</option>
                                @foreach ($modulos as $mod)
                                <option value="{{$mod->codigo}}" @selected($filtros['modulo']==$mod->codigo)>
                                    {{$mod->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="form-group col-6">
                            <label for="status">Estado</label>
                            <select class="form-control" id="status" name="status">
                                <option value="todos">Todos los estados</option>
                                <option value="abiertos" @selected($filtros['status']=='abiertos')>Abiertos (sin asignar + en proceso)</option>
                                <option value="finalizados" @selected($filtros['status']=='finalizados')>Finalizados (resueltos + cerrados)</option>
                                <option value="sin_asignar" @selected($filtros['status']=='sin_asignar')>Sin asignar</option>
                                <option value="en_proceso" @selected($filtros['status']=='en_proceso')>En proceso</option>
                                <option value="resueltos" @selected($filtros['status']=='resueltos')>Resuelto</option>
                                <option value="cerrados" @selected($filtros['status']=='cerrados')>Cerrado</option>
                                <option value="cancelados" @selected($filtros['status']=='cancelados')>Cancelado</option>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="prioridad">Prioridad</label>
                            <select id="prioridad" name="prioridad" class="form-control">
                                <option value="todos">Todas las prioridades</option>
                                <option value="alta" @selected($filtros['prioridad']=='alta')>Alta</option>
                                <option value="media" @selected($filtros['prioridad']=='media')>Media</option>
                                <option value="baja" @selected($filtros['prioridad']=='baja')>Baja</option>
                            </select>
                        </div>
                    </div>
                    @else
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select class="form-control" id="status" name="status">
                            <option value="todos">Todos los estados</option>
                            <option value="abiertos" @selected($filtros['status']=='abiertos')>Abiertos</option>
                            <option value="finalizados" @selected($filtros['status']=='finalizados')>Cerrados</option>
                        </select>
                    </div>
                    @endif
                    

                </form>
                <button onclick="filtrar()" id="filtrar" class="btn btn-success">Aplicar filtros</button>
                <button onclick="eliminar_filtros()" id="elimiar_filtros" class="btn btn-danger ml-3">Eliminar filtros</button>
            </div>
        </div>
      </div>
    </div>


    <form action="{{ route('incidentes') }}" method="GET" id="form_buscar">
        <mi-buscador valor="{{ $buscar }}" form="form_buscar"
        placeholder="Buscar por nombre, DNI o legajo">
        <!-- El cuadro de texto de busqueda se genera con javascript-->
        </mi-buscador>
    </form> 

    @if (count($data)>0)
        <div class="card mb-3">
            @foreach ($data as $value)
            <div class="card ticket 
                @if ($value->prioridad>79 && $value->status->descripcion!='Resuelto' && $value->status->descripcion!='Cerrado') red
                @else
                    @if ($value->status->descripcion=='Sin Asignar') orange
                    @elseif ($value->status->descripcion=='Cerrado' || $value->status->descripcion=='Cancelado') gray
                    @elseif ($value->status->descripcion=='Resuelto') green
                    @else yellow @endif
                @endif">
                <a href="{{ route('incidente.editar', $value->id) }}">
                <div class="m-0">
                    <div class="row m-0 pl-2 pr-2 pt-1">
                        <div class="col-6 p-0 text-left"><p class="text-strong m-0 mb-2">{{ $value->id }}</p></div>
                        <div class="col-6 p-0 text-right text-secondary">
                            @if ($value->status->descripcion!='Sin Asignar')
                                <span class="d-none d-md-inline">
                                    <i class="fa fa-user pr-1" style="font-size:.75rem;position:relative;top:-1px;"></i>
                                    {{ $usuarios[$value->asignado] }} 
                                </span>
                            @endif
                            <span class="m-0 mb-2 ml-3">
                                <i class="fa fa-calendar pr-1" style="font-size:.75rem;position:relative;top:-1px;"></i>
                                {{ substr($value->fecha, 0, 10) }}
                            </span>
                        </div>
                    </div>
                    <div class="row text-secondary text-small ml-0 mb-2 pb-0 mr-2">
                        <p class="col pl-2 pr-2 m-0 pt-0 mb-0 text-truncate"><b>{{ $value->cliente->descripcion }}</b>: {{ $value->short }}</p>

                        <p class="col-auto p-0 ml-2 pb-0 mb-0 @if ($value->prioridad >= 80) text-danger @endif"> 
                            Prioridad: 
                            @if ($value->prioridad < 40) Baja
                            @elseif ($value->prioridad < 80) Media
                            @else Alta
                            @endif
                        </p>
                    </div>

                </div>
                </a>
            </div>
            @endforeach
        </div>
    @else
        No se encontraron incidentes. 
    @endif

    {{ $data->appends(request()->query())->links(true) }}

</div>

<script src="{{ asset('assets/js/buscador-bootstrap.js') }}"></script>
<script>
    $('#filtrarModal').on('shown.bs.modal', function () {
          $('#usuario').trigger('focus')
    })

    function filtrar() {
        document.getElementById("filtrarIncidentes").submit();
    }

    function eliminar_filtros() {
        $('#usuario').val('todos');
        $('#cliente').val('todos');
        $('#tipo_incidente').val('todos');
        $('#modulo').val('todos');
        $('#status').val('todos');
        $('#prioridad').val('todos');
        document.getElementById("filtrarIncidentes").submit();
    }

    function sinAsignar() {
        $('#usuario').val('todos');
        $('#cliente').val('todos');
        $('#tipo_incidente').val('todos');
        $('#modulo').val('todos');
        $('#prioridad').val('todos');
        $('#status').val('sin_asignar');
        document.getElementById("filtrarIncidentes").submit();
    }

</script>

@endsection
