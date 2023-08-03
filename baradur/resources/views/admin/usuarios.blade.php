@extends('layouts.main')

@push('css')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">
@endpush


@section('content')

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Usuarios</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('usuarios.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar usuario</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <form action="{{ route('usuarios.index') }}" method="GET" id="form_buscar" class="col p-0">
      <mi-buscador valor="{{ $buscar }}" form="form_buscar"
      placeholder="Buscar por usuario o nombre">
      <!-- El cuadro de texto de busqueda se genera con javascript-->
      </mi-buscador>
    </form>

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-lg-table-cell" style="width:14rem;">Usuario</th>
            <th class="th-auto">Nombre</th>
            <th class="d-none d-sm-table-cell text-center" style="width:10rem;">Tipo</th>
            <th class="d-none d-md-table-cell text-center" style="width:10rem;">Rol</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($usuarios as $user)
          <tr class="@if($user->activo!=1) text-danger @endif" id="{{$user->idT}}">
            <td class="d-none d-lg-table-cell" >
              <img src="{{ $user->avatar }}" alt=""> {{$user->Usuario}}
            </td>
            <td>{{$user->nombre}}</td>
            <td class="d-none d-sm-table-cell text-center">{{$user->tipo==1? 'Interno':'Externo'}}</td>
            <td class="d-none d-md-table-cell text-center">{{$user->roles->descripcion}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                @if ((Auth::user()->rol==1) || $user->tipo==0 || 
                ($user->rol!=1 && $user->tipo==1 && in_array(101, $permisos)))
                <a href="{{ route('usuarios.edit', $user->idT) }}" class="ri-lg ri-edit-line"></a>
                
                <i onclick="habilitarUsuario('{{ route('usuarios.habilitar', $user->idT) }}')" 
                  class="ri-lg @if($user->activo==1) ri-lock-line @else ri-lock-unlock-line @endif"></i>

                <a href="#" class="ri-lg ri-delete-bin-7-line @if($user->contador>0) disabled @endif" 
                  @if($user->contador==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el usuario?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('usuarios.destroy', $user->idT) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
                @endif
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $usuarios->appends(request()->query())->links(true) }}


    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>

<script src="{{ asset('assets/js/buscador-bootstrap.js') }}"></script>

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
          $('#'+response.idT).children().eq(4).children().eq(1).removeClass('ri-lock-unlock-line');
          $('#'+response.idT).children().eq(4).children().eq(1).addClass('ri-lock-line');
          //$('#'+response.idT).removeClass('bg-light');
          //$('#'+response.idT).children().eq(3).children().eq(1).removeClass('ri-lock-unlock-line');
          //$('#'+response.idT).children().eq(3).children().eq(1).addClass('ri-lock-line');
        }
        else
        {
          $('#'+response.idT).addClass('text-danger');
          $('#'+response.idT).children().eq(4).children().eq(1).removeClass('ri-lock-line');
          $('#'+response.idT).children().eq(4).children().eq(1).addClass('ri-lock-unlock-line');
          //$('#'+response.idT).addClass('bg-light');
          //$('#'+response.idT).children().eq(3).children().eq(1).removeClass('ri-lock-line');
          //$('#'+response.idT).children().eq(3).children().eq(1).addClass('ri-lock-unlock-line');
        }

        //$('#'+response.idT).children().eq(3).text(response.activo==1?'Activo':'Inactivo');
        
      }
    );
  }  
</script>

@endsection