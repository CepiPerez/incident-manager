@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Módulos</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('modulos.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar módulo</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-sm-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th class="d-none d-md-table-cell text-center" style="width:7rem;">Prioridad</th>
            <th class="d-none d-lg-table-cell text-center" style="width:8rem;">Estado</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($modulo as $mod)
          <tr class="@if($mod->activo!=1) text-danger @endif" id="{{$mod->codigo}}">
            <td class="d-none d-sm-table-cell" >{{$mod->codigo}}</td>
            <td class="td-truncated">{{$mod->descripcion}}</td>
            <td class="d-none d-md-table-cell text-center">{{(int)$mod->pondera}}</td>
            <td class="d-none d-lg-table-cell text-center">{{$mod->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('modulos.edit', $mod->codigo) }}" class="ri-lg ri-edit-line"></a>

                <i onclick="habilitarModulo('{{ route('modulos.habilitar', $mod->codigo) }}')" 
                  class="ri-lg @if($mod->activo==1) ri-lock-line @else ri-lock-unlock-line @endif"></i>

                <a href="#" class="ri-lg ri-delete-bin-7-line @if($mod->incidentes_count>0) disabled @endif" 
                  @if($mod->incidentes_count==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el módulo?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('modulos.destroy', $mod->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $modulo->appends(request()->query())->links(true) }}

    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>

<script>

  function habilitarModulo($url)
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
          $('#'+response.codigo).children().eq(4).children().eq(1).removeClass('ri-lock-unlock-line');
          $('#'+response.codigo).children().eq(4).children().eq(1).addClass('ri-lock-line');
        }
        else
        {
          $('#'+response.codigo).addClass('text-danger');
          $('#'+response.codigo).children().eq(4).children().eq(1).removeClass('ri-lock-line');
          $('#'+response.codigo).children().eq(4).children().eq(1).addClass('ri-lock-unlock-line');
        }

        $('#'+response.codigo).children().eq(3).text(response.activo==1?'Activo':'Inactivo');
        
      }
    );
  }  
</script>

@endsection