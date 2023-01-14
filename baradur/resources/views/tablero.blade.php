@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container">

    <div class="row mr-0">
      <h3 class="col pt-2">Tablero de control</h3>
      @if (Auth::user()->rol==1 || count($grupos)>1)
        <button class="col-auto btn btn-filter-slate btn-sm pl-3 pr-3 mt-2" data-toggle="modal" data-target="#filtrarModal">
          @if ( isset($filtros['usuario']) || isset($filtros['grupo']) || isset($filtros['cliente']))
            <i class="ri-information-line mr-1" style="vertical-align:middle;"></i>
            Hay filtros aplicados
          @else
            Filtrar
          @endif
        </button>
      @endif
    </div>
    <hr class="mb-3 mt-0">
    
   
    @if ($contador->total>0)
      <div class="m-0 contador big title mb-3" onclick="abrirEnlace('{{ route('dashboard', 'registrados') }}')">
        <div class="value">{{$contador->total}}</div>
        <div class="text">@if ($contador->sin_asignar==1) incidente @else incidentes @endif registrados</div>
      </div>

      <div style="width:100%; height: 1rem;
        display: grid; margin-bottom: .5rem;
        grid-template-columns: {{$contador->sin_asignar}}fr {{$contador->en_progreso}}fr 
        {{$contador->en_pausa}}fr {{$contador->resueltos}}fr 
        {{$contador->cerrados}}fr {{$contador->cancelados}}fr;
      ">
        <div id="sin_asignar" style="background-color: var(--orange);"></div>
        <div id="en_progreso" style="background-color: var(--yellow);"></div>
        <div id="en_pausa" style="background-color: var(--teal);"></div>
        <div id="resueltos" style="background-color: var(--green);"></div>
        <div id="cerrados" style="background-color: var(--gray);"></div>
        <div id="cancelados" style="background-color: lightgray;"></div>
      </div>

      <div class="d-flex justify-content-start" style="gap:1rem;flex-wrap:wrap;">
        
        <div class="contador" onclick="abrirEnlace('{{ route('dashboard', 'sin_asignar') }}')">
          <div class="value orange">{{$contador->sin_asignar}}</div>
          <div class="text">@if ($contador->sin_asignar==1) incidente @else incidentes @endif sin asignar</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('dashboard', 'en_progreso') }}')">
          <div class="value yellow">{{$contador->en_progreso}}</div>
          <div class="text">@if ($contador->en_progreso==1) incidente @else incidentes @endif en progreso</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('dashboard', 'en_pausa') }}')">
          <div class="value teal">{{$contador->en_pausa}}</div>
          <div class="text">@if ($contador->en_pausa==1) incidente @else incidentes @endif en pausa</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('dashboard', 'resueltos') }}')">
          <div class="value green">{{$contador->resueltos}}</div>
          <div class="text">@if ($contador->resueltos==1) incidente resuelto @else incidentes resueltos @endif</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('dashboard', 'cerrados') }}')">
          <div class="value gray">{{$contador->cerrados}}</div>
          <div class="text">@if ($contador->cerrados==1) incidente cerrado @else incidentes cerrados @endif</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('dashboard', 'cancelados') }}')">
          <div class="value lightgray">{{$contador->cancelados}}</div>
          <div class="text">@if ($contador->cancelados==1) incidente cancelado @else incidentes cancelados @endif</div>
        </div>

      </div>
    @else
      <h5 class="mt-3">No hay incidentes registrados</h5>
    @endif



    @if (($contador->abiertos)>0)
      <div class="m-0 contador big title mt-5 mb-3" onclick="abrirEnlace('{{ route('dashboard', 'abiertos') }}')">
        <div class="value">{{$contador->abiertos}}</div>
        <div class="text">@if (($contador->abiertos)==1) incidente @else incidentes @endif abiertos</div>
      </div>

      <div style="width:100%; height: 1rem;
        display: grid; margin-bottom: .5rem;
        grid-template-columns: {{$contador->en_tiempo}}fr {{$contador->a_vencer}}fr 
        {{$contador->vencidos}}fr;
      ">
        <div id="en_tiempo" style="background-color: var(--green);"></div>
        <div id="a_vencer" style="background-color: var(--orange);"></div>
        <div id="vencidos" style="background-color: var(--red);"></div>
      </div>

      <div class="d-flex justify-content-start" style="gap:1rem;flex-wrap:wrap;">

        <div class="contador big" onclick="abrirEnlace('{{ route('dashboard', 'en_tiempo') }}')">
          <div class="value green">{{$contador->en_tiempo}}</div>
          <div class="text">@if ($contador->en_tiempo==1) incidente @else incidentes @endif dentro del SLA</div>
        </div>

        <div class="contador big" onclick="abrirEnlace('{{ route('dashboard', 'a_vencer') }}')">
          <div class="value orange">{{$contador->a_vencer}}</div>
          <div class="text">@if ($contador->a_vencer==1) incidente @else incidentes @endif próximos a vender</div>
        </div>

        <div class="contador big" onclick="abrirEnlace('{{ route('dashboard', 'vencidos') }}')">
          <div class="value red">{{$contador->vencidos}}</div>
          <div class="text">@if ($contador->vencidos==1) incidente @else incidentes @endif fuera del SLA</div>
        </div>

      </div>
    @endif


    @if ($incidentes)
      <h5 class="pb-1 mt-5" style="font-size:1.5rem;">{{ $status }}</h5>
  
        <table class="table ticketera">
          <thead>
          <tr>
              <th style="width:7rem;">Incidente</th>
              <th class="th-auto">Descripcion</th>
              <th class="d-none d-lg-table-cell" style="width:170px;">Creado</th>
              <th class="d-none d-md-table-cell" style="width:150px;">Asignado</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($incidentes as $value)
              <tr>
                  <td>
                    <a href="{{ route('incidentes.edit', (int)$value->id) }}">
                      <img src="{{asset('assets/icons/'.$value->pid.'.svg')}}" alt="" class="priority">
                      {{ str_pad($value->id, 7, '0', STR_PAD_LEFT) }}
                    </a>
                  </td>
                  <td class="td-truncated">
                    <a href="{{ route('incidentes.edit', (int)$value->id) }}">
                      <span class="mr-2" style="font-weight:600;">{{ $value->cliente->descripcion }}</span>
                      {{ $value->titulo }}
                    </a>
                  </td>
                  <td class="d-none d-lg-table-cell">
                    <a href="{{ route('incidentes.edit', (int)$value->id) }}">
                        <span style="font-weight:500;">{{ $value->fecha_ingreso->rawFormat('d-m-Y') }}</span>
                        <span class="text-secondary" style="font-size:.75rem;">{{ $value->fecha_ingreso->rawFormat(' H:i') }}</span>
                    </a>
                  </td>
                  <td class="d-none d-md-table-cell" class="td-truncated">
                    <a href="{{ route('incidentes.edit', (int)$value->id) }}">
                      @if ($value->status!=0 && $value->asignado->nombre)
                          <img src="{{ $value->asignado->avatar }}" alt="">
                          {{ $value->asignado->nombre }}
                      @else
                          <img src="{{ Storage::url('/public/profile/unassigned.png') }}" alt="">
                          <span style="color:gray;">Sin asignar</span>
                      @endif
                    </a>
                  </td>
                  
              </tr>
          @empty
            <tr class="p-2">
              <td colspan="3">No se encontraron incidentes.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
        {{ $incidentes->appends(request()->query())->links(true) }}
        <br><br>
    @endif

    
  </div>


      <!-- Incident filters Modal -->
      <div class="modal fade" id="filtrarModal" tabindex="-1" role="dialog" 
      aria-labelledby="filtrarModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="min-width:60vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="filtrarModalLabel">Aplicar filtros</h5>
            </div>
            <div class="editor" style="margin:15px;">
              <form action="" method="get" id="dashboardFilters" class="mb-3">


                <div class="form-group col-md p-0">
                  <label for="cliente">Cliente</label>
                  <select class="form-control" id="cliente" name="cliente">
                    <option value="todos">Todos los clientes</option>
                    @foreach ($clientes as $cliente)
                    <option value="{{$cliente->codigo}}" @selected($filtros['cliente']==$cliente->codigo)>{{$cliente->descripcion}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group col-md p-0">
                  <label for="grupo">Grupo asignado</label>
                  <select class="form-control" id="grupo" name="grupo">
                    <option value="todos">Todos los grupos</option>
                    @foreach ($grupos as $grupo)
                    <option value="{{$grupo->codigo}}" @selected($filtros['grupo']==$grupo->codigo)>{{$grupo->descripcion}}</option>
                    @endforeach
                  </select>
                </div>

                @if (Auth::user()->rol==1)
                <div class="form-group col-md p-0 mb-4">
                  <label for="usuario">Usuario asignado</label>
                  <select class="form-control" id="usuario" name="usuario">
                    {{-- <option value="todos">Todos los usuarios</option>
                    @foreach ($usuarios as $user)
                    <option value="{{$user->Usuario}}" @selected($filtros['usuario']==$user->Usuario)>{{$user->nombre}}</option>
                    @endforeach --}}
                  </select>
                </div>
                @endif

              </form>
              <div class="row m-0">
                <button onclick="filtrar()" id="filtrar" class="col-auto btn btn-outline-success">Aplicar filtros</button>
                <button onclick="eliminarFiltros()" id="elimiar_filters" class="col-auto btn btn-outline-danger ml-3">Eliminar filtros</button>
              </div>
            </div>
        </div>
      </div>
  </div>
  

</div>

<script>
  function abrirEnlace($enlace) {
      window.location = $enlace;
  }

  function filtrar() {
    $("#dashboardFilters").submit();
  }

  function eliminarFiltros() {
    $('#cliente').val('todos');
    $('#grupo').val('todos');
    $('#usuario').val('todos');
    $("#dashboardFilters").submit();
  }

  $(document).ready(function(e)
  {
    var obj = <?php echo json_encode($usuarios); ?>;
    var arrayUsuarios = Object.values(obj);

    var obj2 = <?php echo json_encode($grupos); ?>;
    var arrayGrupos = Object.values(obj2);

    var currentUser = '{{ $filtros['usuario']}}';

    $('#grupo').on('change', function ()
    {
      var grupo = this.value;

      $("#usuario").children().remove();

      var div = document.createElement('option');
      div.setAttribute('value', "todos");
      div.innerHTML = "Todos los usuarios";
      document.getElementById("usuario").appendChild(div);

      if (grupo=="todos")
      {
        arrayUsuarios.forEach( function(a)
        {
          var div = document.createElement('option');
          div.setAttribute('value', a.Usuario);
          if (currentUser==a.Usuario)
            div.setAttribute('selected', true);
          div.innerHTML = a.nombre;
          document.getElementById("usuario").appendChild(div);
        });
      }
      else
      {
        arrayGrupos.forEach( function(el)
        {
          if (el.codigo==grupo)
          {
            var users = Object.values(el.miembros);
              
            users.forEach( function(a)
            {
              var div = document.createElement('option');
              div.setAttribute('value', a.Usuario);
              if (currentUser==a.Usuario)
                div.setAttribute('selected', true);
              div.innerHTML = a.nombre;
              document.getElementById("usuario").appendChild(div);
            });
          }
        });
      }


    });

    $('#grupo').change();

  });
</script>
@endsection
