@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Clientes</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('clientes.crear') }}" class="col-auto btn btn-success btn-sm ml-2 mt-2 mb-2 pl-3 pr-3 text-white">
              Agregar cliente</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col w-auto">Cliente</th>
            <th scope="col w-auto" class="d-none d-sm-table-cell">Tipo de servicio</th>
            <th scope="col w-auto" class="d-none d-sm-table-cell">Estado</th>
            <th scope="col" style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($clientes as $cliente)
          <tr class="@if($cliente->activo!=1) text-danger @endif" id="{{$cliente->codigo}}">
            <td>{{$cliente->codigo}}</td>
            <td>{{$cliente->descripcion}}</td>
            <td class="d-none d-sm-table-cell">{{$cliente->servicio}}</td>
            <td class="d-none d-sm-table-cell">{{$cliente->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.75rem;"> 
                <a href="{{ route('clientes.editar', $cliente->codigo) }}" class="fa fa-edit"></a>

                <a href="#{{$cliente->codigo}}" onclick="habilitarCliente('{{ route('clientes.habilitar', $cliente->codigo) }}')" 
                class="fa @if($user->activo==1) fa-lock @else fa-unlock @endif"></a>

                <a href="#" class="fa fa-trash @if($cliente->contador>0) disabled @endif" 
                  @if($cliente->contador==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el cliente?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('clientes.eliminar', $cliente->codigo) }}') &
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