@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Tipos de incidente</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('tipoincidentes.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar tipo de incidente</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-sm-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th class="d-none d-md-table-cell text-center" style="width:7rem;">Prioridad</th>
            <th class="d-none d-md-table-cell text-center" style="width:7rem;">SLA</th>
            <th class="d-none d-lg-table-cell text-center" style="width:8rem;">Estado</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tipo_incidente as $tipo)
          <tr class="@if($tipo->activo!=1) text-danger @endif" id="{{$tipo->codigo}}">
            <td class="d-none d-sm-table-cell" >{{$tipo->codigo}}</td>
            <td class="td-truncated">{{$tipo->descripcion}}</td>
            <td class="d-none d-md-table-cell text-center">{{(int)$tipo->pondera}}</td>
            <td class="d-none d-md-table-cell text-center">{{(int)$tipo->sla}}</td>
            <td class="d-none d-lg-table-cell text-center">{{$tipo->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('tipoincidentes.edit', $tipo->codigo) }}" class="ri-lg ri-edit-line"></a>

                <i onclick="habilitarTipoIncidente('{{ route('tipoincidentes.habilitar', $tipo->codigo) }}')" 
                  class="ri-lg @if($tipo->activo==1) ri-lock-line @else ri-lock-unlock-line @endif"></i>

                <a href="#" class="ri-lg ri-delete-bin-7-line @if($tipo->incidentes_count>0) disabled @endif" 
                  @if($tipo->incidentes_count==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el tipo de incidente?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('tipoincidentes.destroy', $tipo->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $tipo_incidente->appends(request()->query())->links(true) }}

    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>

<script>

  function habilitarTipoIncidente($url)
  {
    console.log("SEND: "+ $url);
    $.ajax({
      url: $url,
      type: 'get'
    })
    .done(
      function(response) { 
        //console.log("RESPONSE:" + response.activo)
        if (response.activo==1)
        {
          $('#'+response.codigo).removeClass('text-danger');
          $('#'+response.codigo).children().eq(5).children().eq(1).removeClass('ri-lock-unlock-line');
          $('#'+response.codigo).children().eq(5).children().eq(1).addClass('ri-lock-line');
        }
        else
        {
          $('#'+response.codigo).addClass('text-danger');
          $('#'+response.codigo).children().eq(5).children().eq(1).removeClass('ri-lock-line');
          $('#'+response.codigo).children().eq(5).children().eq(1).addClass('ri-lock-unlock-line');
        }

        $('#'+response.codigo).children().eq(4).text(response.activo==1?'Activo':'Inactivo');
        
      }
    );
  }  
</script>

@endsection