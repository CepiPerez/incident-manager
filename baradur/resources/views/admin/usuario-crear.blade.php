@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear usuario</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3">


      <form action="{{ route('usuarios.guardar') }}" method="post">
        @csrf

        <div class="form-group">
          <label for="usuario">Nombre de usuario</label>
          <input class="form-control" id="usuario" name="usuario" value="{{ $old->usuario }}" autofocus></input>
        </div>
  
        <div class="form-group">
          <label for="nombre">Nombre y Apellido</label>
          <input class="form-control" id="nombre" name="nombre" value="{{ $old->nombre }}" autocomplete="false"></input>
        </div>

        <div class="form-group">
          <label for="clave">Contraseña</label>
          <input class="form-control" id="clave" name="clave" type="password" value=""></input>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input class="form-control" id="email" name="email" value="{{ $old->email }}" autocomplete="false"></input>
        </div>

        <div class="form-group">
          <label for="cliente">Cliente</label>
          <select id="cliente" name="cliente" class="form-control">
            @foreach ($clientes as $cli)
            <option value="{{$cli->codigo}}" @selected($cli->codigo==$old->cliente)>{{$cli->descripcion}}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="rol">Rol</label>
          <select id="rol" name="rol" class="form-control">
            <option value="admin" @selected($old->rol=='admin')>Administrador</option>
            <option value="cliente" @selected($old->rol=='cliente')>Cliente</option>
            <option value="soporte" @selected($old->rol=='soporte')>Soporte</option>
          </select>
        </div>
  
        <button type="submit" class="col-auto btn btn-primary">Guardar</button>
  
      </form>

    </div>

    
</div>


@endsection
