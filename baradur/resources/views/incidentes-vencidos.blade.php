@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

  <h3 class="p-0 pt-2">Incidentes a reprogramar</h3>
  <hr class="mb-3 mt-0">

  <table class="table ticketera">
    <thead>
    <tr>
      @canany(['admin_tareas', 'inc_backlog'])
        <th style="width:3rem;"></th>
      @endcanany
      <th style="width:7rem;">Incidente</th>
      <th class="th-auto">Descripcion</th>
      <th class="d-none d-lg-table-cell" style="width:170px;">Creado</th>
      <th class="d-none d-md-table-cell" style="width:150px;">Asignado</th>
      <th class="d-none d-md-table-cell text-center" style="width:130px;">Estado</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($incidentes as $value)
        <tr>
          @canany(['admin_tareas', 'inc_backlog'])
            <td>
              <input type="checkbox" name="seleccion[]" value="{{$value->id}}">
            </td>
          @endcanany  
          <td>
            <a href="{{ route('incidentes.show', $value->id) }}">
              <img src="{{asset('assets/icons/'.$value->pid.'.svg')}}" alt="" class="priority">
              {{ str_pad($value->id, 7, '0', STR_PAD_LEFT) }}
            </a>
          </td>
          <td class="td-truncated">
            <a href="{{ route('incidentes.show', $value->id) }}">
              <span class="mr-2" style="font-weight:600;">{{ $value->cli_desc }}</span>
              {{ $value->titulo }}
            </a>
          </td>
          <td class="d-none d-lg-table-cell">
            <a href="{{ route('incidentes.show', $value->id) }}">
                <span style="font-weight:500;">{{ Carbon::parse($value->fecha_ingreso)->rawFormat('d-m-Y') }}</span>
                <span class="text-secondary" style="font-size:.75rem;">{{ Carbon::parse($value->fecha_ingreso)->rawFormat(' H:i') }}</span>
            </a>
          </td>
          <td class="d-none d-md-table-cell" class="td-truncated">
            <a href="{{ route('incidentes.show', $value->id) }}">
              @if ($value->status!=0 && $value->inc_asignado->nombre)
                  <img src="{{ $value->inc_asignado->avatar }}" alt="">
                  {{ $value->inc_asignado->nombre }}
              @else
                  <img src="{{ asset('storage/profile/unassigned.png') }}" alt="">
                  <span class="text-dimm">Sin asignar</span>
              @endif
            </a>
          </td>
          <td class="d-none d-md-table-cell text-center">
            <a href="{{ route('incidentes.show', (int)$value->id) }}">
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
        </tr>
    @empty
      <tr class="p-2">
        <td colspan="3">No se encontraron incidentes.</td>
      </tr>
    @endforelse
    </tbody>
  </table>

  @canany(['admin_tareas', 'inc_backlog'])
    <form class="mt-4" id="multiselect" method="post" action={{route('periodos.mover')}}
      style="transition: all .5s ease; opacity: 0.4">
      @csrf

      <label class="mb-2 mb-md-3"><strong>Mover incidentes seleccionados</strong></label>
      <div class="row">

        <input type="hidden" name="seleccion" id="seleccion">

        <label class="col-12 col-md-auto mt-1" for="mover">Seleccione el sprint</label>
        <select id="mover" name="periodo" class="form-control remitente ml-3 mr-3" disabled>
          <option value="0">Backlog</option>
          @foreach ($periodos as $per)
            <option value="{{$per->codigo}}">{{$per->descripcion}}</option>
          @endforeach
        </select>
      </div>
      <button id="guardar" class="mt-3 btn btn-outline-slate disabled" disabled>Guardar cambios</button>
    </div>
  @endcanany
  
  @if ($incidentes->count()>0)
    {{ $incidentes->appends(request()->query())->links(true) }}
  @endif

  <br>


</div>

@endsection

@push('js')
<script>

  var checkedValues = [];

  $(document).ready(function() {

    var markedCheckbox = document.querySelectorAll('input[type="checkbox"]');
    
    for (var checkbox of markedCheckbox) {
      checkbox.addEventListener('change', (event) => {
        modifyValues(event.currentTarget.value, event.currentTarget.checked)
      });
    }

  });

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

</script>
@endpush