@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Grupos</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('grupos.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar grupo</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-md-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th class="d-none d-sm-table-cell text-center" style="width:8rem;">Miembros</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($grupos as $grupo)
          <tr id="{{$grupo->codigo}}">
            <td class="d-none d-md-table-cell">{{$grupo->codigo}}</td>
            <td>{{$grupo->descripcion}}</td>
            <td class="d-none d-sm-table-cell text-center">{{$grupo->miembros_count}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('grupos.edit', $grupo->codigo) }}" class="ri-lg ri-edit-line"></a>

                <a href="#" class="ri-lg ri-delete-bin-7-line @if($grupo->incidentes_count>0) disabled @endif" 
                  @if($grupo->incidentes_count==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el grupo?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('grupos.destroy', $grupo->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
            
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $grupos->appends(request()->query())->links(true) }}

    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>

{{-- <script>

  function habilitarCliente($url)
  {
    //console.log("SEND: "+ $url);
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
          $('#'+response.codigo).children().eq(3).children().eq(1).removeClass('ri-lock-unlock-line');
          $('#'+response.codigo).children().eq(3).children().eq(1).addClass('ri-lock-line');
        }
        else
        {
          $('#'+response.codigo).addClass('text-danger');
          $('#'+response.codigo).children().eq(3).children().eq(1).removeClass('ri-lock-line');
          $('#'+response.codigo).children().eq(3).children().eq(1).addClass('ri-lock-unlock-line');
        }

        $('#'+response.codigo).children().eq(3).text(response.activo==1?'Activo':'Inactivo');
        
      }
    );
  }  
</script> --}}

@endsection