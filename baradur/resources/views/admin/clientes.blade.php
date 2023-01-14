@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Clientes</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('clientes.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar cliente</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-sm-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Cliente</th>
            <th class="d-none d-lg-table-cell th-auto text-center">Tipo de servicio</th>
            <th class="d-none d-md-table-cell text-center" style="width:8rem;">Estado</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($clientes as $cliente)
          <tr class="@if($cliente->activo!=1) text-danger @endif" id="{{$cliente->codigo}}">
            <td class="d-none d-sm-table-cell" >{{$cliente->codigo}}</td>
            <td>{{$cliente->descripcion}}</td>
            <td class="d-none d-lg-table-cell text-center">{{$cliente->servicio}}</td>
            <td class="d-none d-md-table-cell text-center">{{$cliente->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('clientes.edit', $cliente->codigo) }}" class="ri-lg ri-edit-line"></a>

                <i onclick="habilitarCliente('{{ route('clientes.habilitar', $cliente->codigo) }}')" 
                class="ri-lg @if($cliente->activo==1) ri-lock-line @else ri-lock-unlock-line @endif"></i>

                <a href="#" class="ri-lg ri-delete-bin-7-line @if($cliente->incidentes_count>0) disabled @endif" 
                  @if($cliente->incidentes_count==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el cliente?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('clientes.destroy', $cliente->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
            
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $clientes->appends(request()->query())->links(true) }}

    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>

<script>

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
</script>

@endsection