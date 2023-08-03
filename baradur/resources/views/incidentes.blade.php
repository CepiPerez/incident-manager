@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col pt-2">Incidentes</h3>

        <div class="col-lg botonera pr-0 pl-3">

            @if ($sin_asignar>0 && (!isset($filtros['status']) || $filtros['status']!='sin_asignar') && Auth::user()->tipo==1)
                <a href="#" onclick="sinAsignar()" class="col-auto btn btn-plain danger btn-sm ml-2 mt-3 mb-0 mr-3">
                <i class="ri-error-warning-line mr-1" style="vertical-align:middle;"></i>Hay incidentes sin asignar</a>
            @endif
                
           {{--  <div class="btn-group ml-2">
                <button type="button" class="btn btn-outline-slate btn-sm dropdown-toggle pr-3 pl-3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ordenar</button>
                <div class="dropdown-menu dropdown-menu-right">
                    <button id="order_ia" class="dropdown-item" type="button">Incidente (ascendente)</button>
                    <button id="order_id" class="dropdown-item" type="button">Incidente (descendente)</button>
                    <button id="order_pa" class="dropdown-item" type="button">Prioridad (ascendente)</button>
                    <button id="order_pd" class="dropdown-item" type="button">Prioridad (descendente)</button>
                    <button id="order_fa" class="dropdown-item" type="button">Fecha (ascendente)</button>
                    <button id="order_fd" class="dropdown-item" type="button">Fecha (descendente)</button>
                </div>
            </div> --}}

            @can('crear_inc')
                <a href="{{ route('incidentes.create') }}" 
                    class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-0">
                <i class="ri-add-line mr-1" style="vertical-align:middle;"></i>Nuevo incidente
                </a>
            @endcan

        </div>
    </div>
    <hr class="mb-3 mt-0">


    <!-- Modal para filtrar incidentes -->
    <div class="modal fade" id="filtrarModal" tabindex="-1" role="dialog" 
        aria-labelledby="filtrarModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="min-width:60vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="filtrarModalLabel">Aplicar filtros</h5>
            </div>
            <div class="editor" style="margin:15px;">
                <form action="{{ route('incidentes.index') }}" method="get" id="filtrarIncidentes" class="mb-3">

                    <input type="hidden" id="orden" name="orden" value="">

                    @if (Auth::user()->tipo==1)
                    <div class="row">
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
                        <div class="col-6 form-group">
                            <label for="grupo">Grupo asignado</label>
                            <select class="form-control" id="grupo" name="grupo">
                                <option value="todos">Todos los grupos</option>
                                @foreach ($grupos as $gr)
                                <option value="{{$gr['codigo']}}" @selected($filtros['grupo']==$gr['codigo'])>{{$gr['descripcion']}}</option>
                                @endforeach
                            </select>
                        </div>  
                    </div>


                    <div class="row">    
                        <div class="col-6 form-group">
                            <label for="usuario">Usuario asignado</label>
                            <select class="form-control" id="usuario" name="usuario">
                                {{-- <option value="todos">Todos los usuarios</option>
                                @foreach ($usuarios as $key => $val)
                                <option value="{{$key}}" @selected($filtros['usuario']==$key)>{{$val}}</option>
                                @endforeach --}}
                            </select>
                        </div>

                        <div class="col-6 form-group">
                            <label for="grupo">Area</label>
                            <select class="form-control" id="area" name="area">
                                <option value="todos">Totas las areas</option>
                                @foreach ($areas as $ar)
                                <option value="{{$ar->codigo}}" @selected($filtros['area']==$ar->codigo)>{{$ar->descripcion}}</option>
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
                                <option value="en_backlog" @selected($filtros['status']=='en_backlog')>En backlog</option>
                                <option value="sin_asignar" @selected($filtros['status']=='sin_asignar')>Sin asignar</option>
                                <option value="en_proceso" @selected($filtros['status']=='en_proceso')>En proceso</option>
                                <option value="en_pausa" @selected($filtros['status']=='en_pausa')>En pausa</option>
                                <option value="bloqueados" @selected($filtros['status']=='bloqueados')>Bloqueado</option>
                                <option value="resueltos" @selected($filtros['status']=='resueltos')>Resuelto</option>
                                <option value="cerrados" @selected($filtros['status']=='cerrados')>Cerrado</option>
                                <option value="cancelados" @selected($filtros['status']=='cancelados')>Cancelado</option>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="prioridad">Prioridad</label>
                            <select id="prioridad" name="prioridad" class="form-control">
                                <option value="todos">Todas las prioridades</option>
                                <option value=5 @selected($filtros['prioridad']==5)>Crítica</option>
                                <option value=4 @selected($filtros['prioridad']==4)>Alta</option>
                                <option value=3 @selected($filtros['prioridad']==3)>Media</option>
                                <option value=2 @selected($filtros['prioridad']==2)>Baja</option>
                                <option value=1 @selected($filtros['prioridad']==1)>Sin prioridad</option>
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
                <div class="row m-0">
                    <button onclick="filtrar()" id="filtrar" class="col-auto btn btn-outline-success">Aplicar filtros</button>
                    <button onclick="eliminar_filtros()" id="elimiar_filtros" class="col-auto btn btn-outline-danger ml-3">Eliminar filtros</button>
                    {{-- @if (Auth::user()->tipo==1)
                        <div class="col m-0 p-0 text-right">
                            <button onclick="mis_incidentes()" id="elimiar_filtros" class="btn btn-outline-slate ml-3">Mis tareas</button>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
      </div>
    </div>

    <div class="row m-0 p-0">

        <form action="{{ route('incidentes.index') }}" method="GET" id="form_buscar" class="col p-0 pr-3">
            <mi-buscador valor="{{ $filtros['buscar'] }}" form="form_buscar"
            placeholder="Buscar por número de incidente, título o descripción">
            <!-- El cuadro de texto de busqueda se genera con javascript-->
            </mi-buscador>
        </form>
    
        @if ( isset($filtros['usuario']) || isset($filtros['cliente']) || isset($filtros['status']) 
            || isset($filtros['modulo']) || isset($filtros['tipo_incidente']) || isset($filtros['area'])
            || isset($filtros['prioridad']) || isset($filtros['grupo']) )
            <button class="col-auto btn btn-filter-slate btn-sm pl-3 pr-3"
                data-toggle="modal" data-target="#filtrarModal">
                <i class="ri-information-line mr-1" style="vertical-align:middle;"></i>
                Hay filtros aplicados
            </button>
        @else
            <button class="col-auto btn btn-filter-slate btn-sm pl-3 pr-3"
                data-toggle="modal" data-target="#filtrarModal">
                Filtrar
            </button>
        @endif

    </div>



    @if ($data->count()>0)

        <table class="table ticketera">
            <thead>
            <tr>
                <th class="ordenar" id="{{ $filtros['orden']=='prioridad_asc'? 'prioridad_desc' : 'prioridad_asc' }}" style="width:3.5rem;">
                    P
                    @if ($filtros['orden']=='prioridad_asc')
                        <i class="text-dimm ri-arrow-down-s-line"></i>
                    @elseif ($filtros['orden']=='prioridad_desc')
                        <i class="text-dimm ri-arrow-up-s-line"></i>
                    @else
                        <i class="text-dimm ri-arrow-down-s-line unselect"></i>
                    @endif
                </th>
                <th class="ordenar" id="{{ $filtros['orden']=='incidentes_asc'? 'incidentes_desc' : 'incidentes_asc' }}" style="width:6rem;padding-left:0;">
                    Inc
                    @if ($filtros['orden']=='incidentes_asc')
                        <i class="text-dimm ri-arrow-down-s-line"></i>
                    @elseif ($filtros['orden']=='incidentes_desc')
                        <i class="text-dimm ri-arrow-up-s-line"></i>
                    @else
                        <i class="text-dimm ri-arrow-down-s-line unselect"></i>
                    @endif
                </th>
                <th class="ordenar th-auto" id="{{ $filtros['orden']=='cliente_asc'? 'cliente_desc' : 'cliente_asc' }}">
                    Descripcion
                    @if ($filtros['orden']=='cliente_asc')
                        <i class="text-dimm ri-arrow-down-s-line"></i>
                    @elseif ($filtros['orden']=='cliente_desc')
                        <i class="text-dimm ri-arrow-up-s-line"></i>
                    @else
                        <i class="text-dimm ri-arrow-down-s-line unselect"></i>
                    @endif
                </th>
                <th class="ordenar d-none d-xl-table-cell" id="{{ $filtros['orden']=='fecha_asc'? 'fecha_desc' : 'fecha_asc' }}" style="width:170px;">
                    Creado
                    @if ($filtros['orden']=='fecha_asc')
                        <i class="text-dimm ri-arrow-down-s-line"></i>
                    @elseif ($filtros['orden']=='fecha_desc')
                        <i class="text-dimm ri-arrow-up-s-line"></i>
                    @else
                        <i class="text-dimm ri-arrow-down-s-line unselect"></i>
                    @endif
                </th>
                <th class="ordenar d-none d-lg-table-cell" id="{{ $filtros['orden']=='asignado_asc'? 'asignado_desc' : 'asignado_asc' }}" style="width:150px;">
                    Asignado
                    @if ($filtros['orden']=='asignado_asc')
                        <i class="text-dimm ri-arrow-down-s-line"></i>
                    @elseif ($filtros['orden']=='asignado_desc')
                        <i class="text-dimm ri-arrow-up-s-line"></i>
                    @else
                        <i class="text-dimm ri-arrow-down-s-line unselect"></i>
                    @endif
                </th>
                <th class="ordenar d-none d-md-table-cell text-center" id="{{ $filtros['orden']=='estado_asc'? 'estado_desc' : 'estado_asc' }}" style="width:120px;">
                    Estado
                    @if ($filtros['orden']=='estado_asc')
                        <i class="text-dimm ri-arrow-down-s-line"></i>
                    @elseif ($filtros['orden']=='estado_desc')
                        <i class="text-dimm ri-arrow-up-s-line"></i>
                    @else
                        <i class="text-dimm ri-arrow-down-s-line unselect"></i>
                    @endif
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $value)
                <tr>
                    <td class="pl-1">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                            <img src="{{asset('assets/icons/'.$value->pid.'.svg')}}" alt="" class="priority">
                        </a>
                    </td>
                    <td class="pl-0 text-secondary">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        {{ str_pad($value->id, 7, '0', STR_PAD_LEFT) }}
                        </a>                        
                    </td>
                    <td class="td-truncated">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        @if (Auth::user()->tipo==1)
                        <span class="mr-2" style="font-weight:600;">{{ $value->cliente_desc }}</span>
                        {{ $value->titulo }}
                        @else
                        <span class="mr-2 text-secondary" style="font-weight:500;">{{ $value->titulo }}</span>
                        @endif
                        </a>
                    </td>
                    <td class="d-none d-xl-table-cell">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        <span style="font-weight:500;">{{ $value->fecha_ingreso->rawFormat('d-m-Y') }}</span>
                        <span class="text-secondary" style="font-size:.75rem;">{{ $value->fecha_ingreso->rawFormat(' H:i') }}</span>
                        </a>
                    </td>
                    <td class="d-none d-lg-table-cell td-truncated">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        @if ($value->status!=0 && $value->asignado)
                            <img src="{{ $value->inc_asignado->avatar }}" alt="">
                            <span class="text-secondary">{{ $value->inc_asignado->nombre }}</span>
                        @else
                            <img src="{{ Storage::url('/profile/unassigned.png') }}" alt="">
                            <span class="text-dimm">Sin asignar</span>
                        @endif
                        </a>
                    </td>
                    <td class="d-none d-md-table-cell text-center">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        <i class="badge
                        @if ($value->periodo==="0") badge-pink
                        @elseif ($value->status==0) badge-orange
                        @elseif ($value->status==5) badge-teal
                        @elseif ($value->status==6) badge-red
                        @elseif ($value->status==10) badge-green
                        @elseif ($value->status==20) badge-gray
                        @elseif ($value->status==50) badge-lightgray
                        @else badge-blue
                        @endif
                        ">{{ $value->periodo==="0" ? 'en backlog' : $value->status_desc }}</i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="d-none d-md-flex mb-0 referencias">
            <p><i>Prioridades:</i></p>
            @foreach (Prioridad::all() as $p)
                <p class="ml-3"><img src="{{asset('assets/icons/'.$p->id.'.svg')}}" alt="" class="priority mr-2">{{$p->descripcion}}</p>
            @endforeach
        </div>
    @else
        No se encontraron incidentes. 
    @endif

    @if ($data->hasMorePages())
    {{ $data->appends(request()->query())->links(true) }}
    @endif

</div>

<script src="{{ asset('assets/js/buscador-bootstrap.js') }}"></script>
<script>
    $('#filtrarModal').on('shown.bs.modal', function () {
          $('#cliente').trigger('focus')
    })

    function filtrar() {
        document.getElementById("filtrarIncidentes").submit();
    }

    function eliminar_filtros() {
        $('#cliente').val('todos');
        $('#grupo').val('todos');
        $('#area').val('todas');
        $('#usuario').val('todos');
        $('#tipo_incidente').val('todos');
        $('#modulo').val('todos');
        $('#status').val('todos');
        $('#prioridad').val('todos');
        document.getElementById("filtrarIncidentes").submit();
    }

    function mis_incidentes() {
        $('#usuario').val('{{Auth::user()->Usuario}}');
        $('#cliente').val('todos');
        $('#area').val('todas');
        $('#tipo_incidente').val('todos');
        $('#modulo').val('todos');
        $('#status').val('abiertos');
        $('#prioridad').val('todos');
        document.getElementById("filtrarIncidentes").submit();
    }

    function sinAsignar() {
        $('#usuario').val('todos');
        $('#cliente').val('todos');
        $('#area').val('todas');
        $('#tipo_incidente').val('todos');
        $('#modulo').val('todos');
        $('#prioridad').val('todos');
        $('#status').val('sin_asignar');
        document.getElementById("filtrarIncidentes").submit();
    }

    $('#order_ia').on('click', function () {
        $('#orden').val('incidentes_asc');
        document.getElementById("filtrarIncidentes").submit();
    })

    $('#order_id').on('click', function () {
        $('#orden').val('incidentes_desc');
        document.getElementById("filtrarIncidentes").submit();
    })

    $('#order_pa').on('click', function () {
        $('#orden').val('prioridad_asc');
        document.getElementById("filtrarIncidentes").submit();
    })

    $('#order_pd').on('click', function () {
        $('#orden').val('prioridad_desc');
        document.getElementById("filtrarIncidentes").submit();
    })

    $('#order_fa').on('click', function () {
        $('#orden').val('fecha_asc');
        document.getElementById("filtrarIncidentes").submit();
    })

    $('#order_fd').on('click', function () {
        $('#orden').val('fecha_desc');
        document.getElementById("filtrarIncidentes").submit();
    })

    $('.ordenar').on('click', function () {
        //console.log($(this).attr('id'))

        $('#orden').val($(this).attr('id'));
        document.getElementById("filtrarIncidentes").submit();
        
    })

    var obj2 = <?php echo json_encode($grupos); ?>;
    var arrayGrupos = Object.values(obj2);

    var obj3 = <?php echo json_encode($usuarios); ?>;
    var arrayUsuarios = Object.values(obj3);

    $('#grupo').on('change', function ()
    {
      $("#usuario").children().remove();

      var div = document.createElement('option');
      div.setAttribute('value', "todos");
      div.innerHTML = "Todos los usuarios";
      document.getElementById("usuario").appendChild(div);

      var grupo = this.value;
      var users = undefined;

      if (grupo=='todos')
      {
        Object.keys(obj3).forEach(key => {
          var div = document.createElement('option');
          div.setAttribute('value', key);
          div.innerHTML = obj3[key];
          if (key=='{{$filtros['usuario']}}')
              div.setAttribute('selected', true);
          document.getElementById("usuario").appendChild(div);
        });
      }
      else
      {
        arrayGrupos.forEach( function(el)
        {
          if (el.codigo==grupo)
            users = Object.values(el.miembros);
        });

        users.forEach( function(a)
        {
          var div = document.createElement('option');
          div.setAttribute('value', a.Usuario);
          div.innerHTML = a.nombre;
          if (a.codigo=='{{$filtros['usuario']}}')
              div.setAttribute('selected', true);
          document.getElementById("usuario").appendChild(div);
        });
      }


    });

    $(document).ready(function(e)
    {
        $('#grupo').change();
    });

</script>

@endsection
