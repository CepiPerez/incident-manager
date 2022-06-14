@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar usuario</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3">

      <h4 class="">{{ $usuario->Usuario }}</h4>
      <hr class="mt-0">

      <form action="{{ route('usuarios.modificar', $usuario->idT) }}" method="post" autocomplete="off">
        @csrf
        @method('put')

  
        <div class="form-group">
          <label for="nombre">Nombre Completo</label>
          <input class="form-control" id="nombre" name="nombre" value="{{ $usuario->nombre }}"></input>
        </div>

        <div class="form-group">
          <label for="clave">Contraseña <span class="text-secondary">(dejar en blanco para mantener la actual)</span></label>
          <input class="form-control" id="clave" name="clave" type="password" value=""></input>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input class="form-control" id="email" name="email" value="{{ $usuario->Mail }}" autocomplete="false"></input>
        </div>

        <div class="form-group">
          <label for="cliente">Cliente</label>
          <select id="cliente" name="cliente" class="form-control">
            @foreach ($clientes as $cli)
            <option value="{{$cli->codigo}}" @selected($usuario->cliente==$cli->codigo)>{{$cli->descripcion}}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="rol">Rol</label>
          <select id="rol" name="rol" class="form-control">
            <option value="admin" @selected($usuario->rol=='admin')>Administrador</option>
            <option value="cliente" @selected($usuario->rol=='cliente')>Cliente</option>
            <option value="soporte" @selected($usuario->rol=='soporte')>Soporte</option>
          </select>
        </div>
  
        <button type="submit" class="col-auto btn btn-primary">Guardar cambios</button>
  
      </form>

    </div>

    
</div>


@endsection
