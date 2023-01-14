@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear usuario</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('usuarios.store') }}" method="post">
        @csrf
        
        <div class="row m-0">
          <div class="form-group col-md p-0 mr-0 mr-md-3">
            <label for="usuario">Nombre de usuario</label>
            <input class="form-control" id="usuario" name="usuario" value="{{ $old->usuario }}" autofocus>
          </div>
          <div class="form-group col-md p-0">
            <label for="nombre">Nombre y Apellido</label>
            <input class="form-control" id="nombre" name="nombre" value="{{ $old->nombre }}" autocomplete="false">
          </div>
        </div>

        <div class="row m-0">
          <div class="form-group col-md p-0 mr-0 mr-md-3">
            <label for="email">Email</label>
            <input class="form-control" id="email" name="email" value="{{ $old->email }}" autocomplete="false">
          </div>
          <div class="form-group col-md p-0">
            <label for="clave">Contraseña</label>
            <input class="form-control" id="clave" name="clave" type="password" value="">
          </div>
        </div>

        <div class="row m-0">
          <div class="form-group col-md p-0 mr-0 mr-md-3">
            <label for="tipo">Tipo de usuario</label>
            <select id="tipo" name="tipo" class="form-control">
              <option value=1 @selected($old->tipo==1)>Interno</option>
              <option value=0 @selected($old->tipo==0)>Externo</option>
            </select>
          </div>
          <div class="form-group col-md p-0">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" class="form-control">
              @foreach ($roles as $rol)
              <option value="{{$rol->id}}" @selected($old->rol==$rol->id)>{{$rol->descripcion}}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row m-0">
          <div class="form-group col-md p-0 mr-0 mr-md-3" id="gcliente">
            <label for="cliente">Cliente</label>
            <select id="cliente" name="cliente" class="form-control">
              @foreach ($clientes as $cli)
              <option value="{{$cli->codigo}}" @selected($cli->codigo==$old->cliente)>{{$cli->descripcion}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md p-0">
          </div>
        </div>
        
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar</button>
  
      </form>

    </div>

    
</div>

<script>

  $(document).ready(function(e)
  {

    $('#tipo').on('change', function ()
    {
      //console.log("TIPO: "+this.value);

      admin = $("#rol").children().eq(0);
      if (this.value==0)
      {
        admin.attr('disabled', true);
        $('#gcliente').attr('hidden', false);

        if (admin.is(':selected'))
        {
            next = $("#rol").children().eq(1); 
            $("#rol").val(next.val());
        }
      }
      else
      {
        admin.attr('disabled', false);
        $('#gcliente').attr('hidden', true);
      }

    });

    $('#tipo').change();

  });


</script>


</script>

@endsection
