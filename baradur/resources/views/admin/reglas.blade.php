@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Reglas de prioridad</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('reglas.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar regla</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    @if ($reglas->count()>0)
    <table class="table">
        <thead>
          <tr>
            <th style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th class="d-none d-md-table-cell text-center" style="width:7rem;">Prioridad</th>
            <th class="d-none d-lg-table-cell text-center" style="width:8rem;">Estado</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($reglas as $tipo)
          <tr class="@if($tipo->activo!=1) text-danger @endif" id="{{$tipo->id}}">
            <td>{{$tipo->id}}</td>
            <td>{{$tipo->descripcion}}</td>
            <td class="d-none d-md-table-cell text-center">{{$tipo->pondera}}</td>
            <td class="d-none d-lg-table-cell text-center">{{$tipo->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('reglas.edit', $tipo->id) }}" class="ri-edit-box-line"></a>

                <i onclick="habilitarRegla('{{ route('reglas.habilitar', $tipo->id) }}')" 
                  class="@if($tipo->activo==1) ri-lock-line @else ri-lock-unlock-line @endif"></i>

                <a href="#" class="ri-delete-bin-7-line @if($user->contador>0) disabled @endif" 
                  @if($user->contador==0)
                    onclick="window.confirm('Esta seguro que desea eliminar la regla?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('reglas.destroy', $tipo->id) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>
    @else
      <p>No se encontraron reglas</p>
    @endif


    {{ $reglas->appends(request()->query())->links(true) }}


    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>

<script>

  var slider = document.getElementById("prioridad");
  var output = document.getElementById("texto_prioridad");
  //output.innerHTML = 'Prioridad: ' slider.value;

  // Update the current slider value (each time you drag the slider handle)
  slider.oninput = function() {
    output.innerHTML = 'Prioridad: ' + this.value;
  }


  function habilitarRegla($url)
  {
    //console.log("SEND: "+ $url);
    $.ajax({
      url: $url,
      type: 'get'
    })
    .done(
      function(response) { 
        if (response.activo==1)
        {
          $('#'+response.id).removeClass('text-danger');
          $('#'+response.id).children().eq(3).children().eq(1).removeClass('ri-lock-unlock-line');
          $('#'+response.id).children().eq(3).children().eq(1).addClass('ri-lock-line');
        }
        else
        {
          $('#'+response.id).addClass('text-danger');
          $('#'+response.id).children().eq(3).children().eq(1).removeClass('ri-lock-line');
          $('#'+response.id).children().eq(3).children().eq(1).addClass('ri-lock-unlock-line');
        }

        $('#'+response.id).children().eq(3).text(response.activo==1?'Activo':'Inactivo');
      }
    );
  }  

</script>

@endsection