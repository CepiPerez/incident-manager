@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Usuarios</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('usuarios.crear') }}" class="col-auto btn btn-success btn-sm ml-2 mt-2 mb-2 pl-3 pr-3 text-white">
              Agregar usuario</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th scope="col">Usuario</th>
            <th scope="col w-auto">Nombre</th>
            <th scope="col w-auto" class="d-none d-sm-table-cell">Rol</th>
            <th scope="col w-auto" class="d-none d-sm-table-cell">Cliente</th>
            <th scope="col" style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($usuarios as $user)
          <tr class="@if($user->activo!=1) text-danger @endif" id="{{$user->idT}}">
            <td>{{$user->Usuario}}</td>
            <td>{{$user->nombre}}</td>
            <td class="d-none d-sm-table-cell">{{$user->rol}}</td>
            <td class="d-none d-sm-table-cell">{{$user->cliente->descripcion}}</td>
            <td class="text-right no-pointer" style="word-spacing:.75rem;"> 
                <a href="{{ route('usuarios.editar', $user->idT) }}" class="fa fa-edit"></a>

                <a href="#" onclick="habilitarUsuario('{{ route('usuarios.habilitar', $user->idT) }}')" class="fa @if($user->activo==1) fa-lock @else fa-unlock @endif"></a>

                <a href="#" class="fa fa-trash @if($user->contador>0) disabled @endif" 
                  @if($user->contador==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el usuario?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('usuarios.eliminar', $user->idT) }}') &
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

  function habilitarUsuario($url)
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
          $('#'+response.idT).removeClass('text-danger');
          $('#'+response.idT).children().eq(4).children().eq(1).removeClass('fa-unlock');
          $('#'+response.idT).children().eq(4).children().eq(1).addClass('fa-lock');
        }
        else
        {
          $('#'+response.idT).addClass('text-danger');
          $('#'+response.idT).children().eq(4).children().eq(1).removeClass('fa-lock');
          $('#'+response.idT).children().eq(4).children().eq(1).addClass('fa-unlock');
        }

        $('#'+response.idT).children().eq(3).text(response.activo==1?'Activo':'Inactivo');
        
      }
    );
  }  
</script>

@endsection