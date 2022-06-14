@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Tipos de incidente</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('tipoincidente.crear') }}" class="col-auto btn btn-success btn-sm ml-2 mt-2 mb-2 pl-3 pr-3 text-white">
              Agregar tipo de incidente</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col w-auto">Descripcion</th>
            <th scope="col w-auto" class="d-none d-sm-table-cell">Estado</th>
            <th scope="col" style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tipo_incidente as $tipo)
          <tr class="@if($tipo->activo!=1) text-danger @endif" id="{{$tipo->codigo}}">
            <td>{{$tipo->codigo}}</td>
            <td>{{$tipo->descripcion}}</td>
            <td class="d-none d-sm-table-cell">{{$tipo->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.75rem;"> 
                <a href="{{ route('tipoincidente.editar', $tipo->codigo) }}" class="fa fa-edit"></a>

                <a href="#" onclick="habilitarTipoIncidente('{{ route('tipoincidente.habilitar', $tipo->codigo) }}')" class="fa @if($tipo->activo==1) fa-lock @else fa-unlock @endif"></a>

                <a href="#" class="fa fa-trash @if($tipo->contador>0) disabled @endif" 
                  @if($tipo->contador==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el tipo de incidente?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('tipoincidente.eliminar', $tipo->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

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
          $('#'+response.codigo).children().eq(3).children().eq(1).removeClass('fa-unlock');
          $('#'+response.codigo).children().eq(3).children().eq(1).addClass('fa-lock');
        }
        else
        {
          $('#'+response.codigo).addClass('text-danger');
          $('#'+response.codigo).children().eq(3).children().eq(1).removeClass('fa-lock');
          $('#'+response.codigo).children().eq(3).children().eq(1).addClass('fa-unlock');
        }

        $('#'+response.codigo).children().eq(2).text(response.activo==1?'Activo':'Inactivo');
        
      }
    );
  }  
</script>

@endsection