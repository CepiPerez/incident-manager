@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

  @if($periodo->codigo==0)
    <h3 class="col-sm pt-2 p-0">Backlog</h3>
  @else
    <div class="row mr-0">
        <h3 class="col-sm pt-2">{{ $periodo->descripcion }}</h3>
        @can('crear_periodos')
        <div class="col-sm botonera pr-0">

          <a href="{{route('periodos.edit', $periodo->codigo)}}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-edit-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Editar este sprint
          </a>

          <span class="col-auto btn btn-plain danger btn-sm ml-2 mt-3 mb-1"
            onclick="window.confirm('Esta seguro que desea eliminar el sprint?')?
            (document.getElementById('form-delete').setAttribute('action','{{ route('periodos.destroy', $periodo->codigo) }}') &
            document.getElementById('form-delete').submit()):''"
            >
            <i class="ri-delete-bin-7-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Eliminar este sprint
          </span>
        </div>
        @endcan
    </div>
  @endif

  <hr class="mb-3 mt-0">
  
  @if(intval($periodo->codigo)>0)
    <form id="form-delete" method="post" action="" class="d-none">
      @csrf 
      @method('delete')
    </form>

    @if ($total > 0)

      <div class="m-0 contador big title mb-3"
        onclick="abrirEnlace('{{ route('periodos.show', $periodo->codigo) }}', 'asignados')">
        <div class="value">{{$total}}</div>
        <div class="text">@if ($total==1) Incidente asignado @else Incidentes asignados @endif al sprint</div>
      </div>
      
      <div style="width:100%; height: 1rem;
        display: grid; margin-bottom: .5rem;
        grid-template-columns: {{$cerrados}}fr {{$abiertos}}fr {{$bloqueados}}fr {{$cancelados}}fr;
      ">
        <div id="cerrados" class="background-green"></div>
        <div id="abiertos" class="background-orange"></div>
        <div id="bloqueados" class="background-red"></div>
        <div id="cancelados" class="background-dimm"></div>
      </div>

      <div class="d-flex justify-content-start" style="gap:1rem;flex-wrap:wrap;">
        
        <div class="contador" onclick="abrirEnlace('{{ route('periodos.show', $periodo->codigo) }}', 'finalizados')">
          <div class="value text-green">{{$cerrados}}</div>
          <div class="text">@if ($cerrados==1) incidente finalizado @else incidentes finalizados @endif</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('periodos.show', $periodo->codigo) }}', 'pendientes')">
          <div class="value text-orange">{{$abiertos}}</div>
          <div class="text">@if ($abiertos==1) incidente pendiente @else incidentes pendientes @endif</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('periodos.show', $periodo->codigo) }}', 'bloqueados')">
          <div class="value text-red">{{$bloqueados}}</div>
          <div class="text">@if ($bloqueados==1) incidente bloqueado @else incidentes bloqueados @endif</div>
        </div>

        <div class="contador" onclick="abrirEnlace('{{ route('periodos.show', $periodo->codigo) }}', 'cancelados')">
          <div class="value text-dimm">{{$cancelados}}</div>
          <div class="text">@if ($cancelados==1) incidente cancelado @else incidentes cancelados @endif</div>
        </div>

      </div>

      <br><br>
      <h4>Datos por usuario</h4>
      <hr style="margin-top: 0">
      <div class="row mt-2 m-0">
        @foreach ($usuarios as $usuario)
          <div class="sprint-user col-6 col-lg-4 col-xl-3 p-1">
            <div class="d-flex card pt-2 pb-2">
              <div class="row">
                <div class="col-auto ml-3 pr-0">
                  <img src="{{ $usuario['data']->avatar }}" height="60" 
                    style="border-radius: 50%; object-fit: cover; overflow: hidden;">
                </div>
                <div class="col mr-3">
                  <div><strong>{{$usuario['data']->nombre}}</strong></div>
                  <div>
                    Completado: {{$usuario['incidentes']->where('status', '>=', 10)->count()}}/{{$usuario['incidentes']->count()}}
                  </div>
                  <div style="width:100%; height: .75rem; margin-top: .35rem;
                      display: grid; margin-bottom: 0rem;
                      grid-template-columns: {{$usuario['incidentes']->where('status', '>=', 10)->count()}}fr 
                      {{$usuario['incidentes']->where('status', '<', 10)->count()}}fr;
                    ">
                      <div class="background-green"></div>
                      <div class="background-orange"></div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        @endforeach
      </div>

    @endif

    <h5 class="pb-1 mt-5" style="font-size:1.5rem;">{{$titulo}}</h5>
  @endif

  <table class="table ticketera">
    <thead>
    <tr>
        @can('admin_tareas')
          @if($periodo->codigo===0 && $periodos->count()>0)
            <th style="width:3rem;"></th>
          @endif
        @endcan
        <th style="width:7rem;">Incidente</th>
        <th class="th-auto">Descripcion</th>
        <th class="d-none d-lg-table-cell" style="width:170px;">Creado</th>
        <th class="d-none d-md-table-cell" style="width:150px;">Asignado</th>
        @if($periodo->codigo!=0)
          <th class="d-none d-md-table-cell text-center" style="width:130px;">Estado</th>
        @endif
        {{-- <th class="d-none d-md-table-cell" style="width:130px;">Resolución</th> --}}
    </tr>
    </thead>
    <tbody>
    @forelse ($incidentes as $value)
        <tr>
            @can('admin_tareas')
              @if($periodo->codigo===0 && $periodos->count()>0)
                <td>
                  <input type="checkbox" name="seleccion[]" value="{{$value->id}}">
                </td>
              @endif
            @endcan
            <td>
              <a href="{{ route('incidentes.show', $periodo->codigo==0 ? (int)$value->id : (int)$value->incidente) }}">
                <img src="{{asset('assets/icons/'.$value->pid.'.svg')}}" alt="" class="priority">
                {{ str_pad($periodo->codigo==0? $value->id : $value->incidente, 7, '0', STR_PAD_LEFT) }}
              </a>
            </td>
            <td class="td-truncated">
              <a href="{{ route('incidentes.show', $periodo->codigo==0 ? (int)$value->id : (int)$value->incidente) }}">
                <span class="mr-2" style="font-weight:600;">{{ $value->cli_desc }}</span>
                {{ $value->titulo }}
              </a>
            </td>
            <td class="d-none d-lg-table-cell">
              <a href="{{ route('incidentes.show', $periodo->codigo==0 ? (int)$value->id : (int)$value->incidente) }}">
                  <span style="font-weight:500;">{{ Carbon::parse($value->fecha_ingreso)->rawFormat('d-m-Y') }}</span>
                  <span class="text-secondary" style="font-size:.75rem;">{{ Carbon::parse($value->fecha_ingreso)->rawFormat(' H:i') }}</span>
              </a>
            </td>
            <td class="d-none d-md-table-cell" class="td-truncated">
              <a href="{{ route('incidentes.show', $periodo->codigo==0 ? (int)$value->id : (int)$value->incidente) }}">
                @if ($value->status!=0 && $value->inc_asignado->nombre)
                    <img src="{{ $value->inc_asignado->avatar }}" alt="">
                    {{ $value->inc_asignado->nombre }}
                @else
                    <img src="{{ asset('storage/profile/unassigned.png') }}" alt="">
                    <span class="text-dimm">Sin asignar</span>
                @endif
              </a>
            </td>
            @if($periodo->codigo!=0)
              <td class="d-none d-md-table-cell text-center">
                <a href="{{ route('incidentes.show', $periodo->codigo==0 ? (int)$value->id : (int)$value->incidente) }}">
                <i class="badge
                @if ($value->status==0) badge-orange
                @elseif ($value->status==5) badge-teal
                @elseif ($value->status==6) badge-red
                @elseif ($value->status==10) badge-green
                @elseif ($value->status==20) badge-gray
                @elseif ($value->status==50) badge-lightgray
                @else badge-blue
                @endif
                ">{{ $value->status_desc }}</i>
                </a>
              </td>
            @endif
            {{-- <td class="d-none d-lg-table-cell">
              <a href="{{ route('incidentes.show', $periodo->codigo==0 ? (int)$value->id : (int)$value->incidente) }}">
                @if ($value->status>=10)
                  @if ($value->avances_estimado)
                    <span class="text-secondary">
                      @if (Carbon::parse($value->avances()->where('tipo_avance', '>=', 10)->first()->fecha_ingreso) > $value->avances_estimado->descripcion)
                        Fuera de tiempo
                      @else
                        En tiempo
                      @endif
                    </span>
                  @else
                    No estimado
                  @endif
                @elseif ($value->avances_estimado)
                  <span style="font-weight:500;">{{ $value->avances_estimado->descripcion->rawFormat('d-m-Y') }}</span>
                  <span class="text-secondary" style="font-size:.75rem;">{{ $value->avances_estimado->descripcion->rawFormat(' H:i') }}</span>
                @else
                  <span class="text-dimm">No estimado</span>
                @endif
              </a>
            </td> --}}
        </tr>
    @empty
      <tr class="p-2">
        <td colspan="3">No se encontraron incidentes.</td>
      </tr>
    @endforelse
    </tbody>
  </table>
  
  
  @if ($incidentes->count()>0)
    {{ $incidentes->appends(request()->query())->links(true) }}
  @endif

  
  @can('admin_tareas')
    @if($periodo->codigo===0 && $periodos->count()>0)
      <form class="mt-4" id="multiselect" method="post" action={{route('periodos.mover')}}
        style="transition: all .5s ease; opacity: 0.4">
        @csrf

        <label class="mb-2 mb-md-3"><strong>Mover incidentes seleccionados</strong></label>
        <div class="row">

          <input type="hidden" name="seleccion" id="seleccion">

          <label class="col-12 col-md-auto mt-1" for="mover">Seleccione el sprint</label>
          <select id="mover" name="periodo" class="form-control remitente ml-3 mr-3" disabled>
            @foreach ($periodos as $per)
              <option value="{{$per->codigo}}">{{$per->descripcion}}</option>
            @endforeach
          </select>
        </div>
        <button id="guardar" class="mt-3 btn btn-outline-slate disabled" disabled>Guardar cambios</button>
      </form>

    @else
      <br>
    @endif
  @else
    <br>
  @endcan

</div>


<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/newdatetimepicker.css') }}">
<script src="{{ asset('assets/js/newdatetimepicker.js') }}"></script>

<script>

  var checkedValues = [];

  function abrirEnlace(enlace, filtro) {
      window.location = enlace + '?filtro=' + filtro;
  }

  function modifyValues(value, checked) {
    if (checked) {
      checkedValues.push(value);
    } else {
      const index = checkedValues.indexOf(value);
      if (index > -1) {
        checkedValues.splice(index, 1);
      }
    }
    
    $('#mover').attr('disabled', checkedValues.length==0);
    $('#guardar').attr('disabled', checkedValues.length==0);
    
    if (checkedValues.length==0) {
      $('#guardar').addClass('disabled');
      $('#multiselect').css('opacity', '0.4');
    } else {
      $('#guardar').removeClass('disabled');
      $('#multiselect').css('opacity', '1');
    }

    $('#seleccion').val(checkedValues);

  }


  $(document).ready(function(e)
  {
    var markedCheckbox = document.querySelectorAll('input[type="checkbox"]');
    
    for (var checkbox of markedCheckbox) {
      checkbox.addEventListener('change', (event) => {
        modifyValues(event.currentTarget.value, event.currentTarget.checked)
      });
    }

    $('#desde').datetimepicker({
      format:'d-m-Y',
      timepicker:false,
      formatDate:'d-m-Y'
    });
  
    $('#hasta').datetimepicker({
      format:'d-m-Y',
      timepicker:false,
      formatDate:'d-m-Y'
    });

  });

</script>

@endsection