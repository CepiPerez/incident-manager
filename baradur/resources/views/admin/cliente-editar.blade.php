@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar cliente</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">

      <form action="{{ route('clientes.update', $cliente->codigo) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Nombre del cliente</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $cliente->descripcion }}"></input>
        </div>


        <div class="form-group col-6 pl-0">
          <label for="tipo_servicio">Tipo de servicio</label>
          <select id="tipo_servicio" name="tipo_servicio" class="form-control">
            @foreach ($tipo_servicio as $ti)
              <option value="{{$ti->codigo}}" @selected($cliente->tipo_servicio==$ti->codigo)>
                  {{$ti->descripcion}}</option>
            @endforeach
          </select>
        </div>

        <h5 class="mt-4">Areas</h5>
        <hr class="mt-0">
        @foreach ($areas as $area)
        <div class="form-check pb-2 ml-0">
          <input type="checkbox" class="form-check-input" name="areas[]" id="{{$area->codigo}}"
            value="{{$area->codigo}}" @checked( in_array($area->codigo, $areas_cliente) )>
          <label class="ml-2 form-check-label" style="padding-top:1px;" 
            onclick="document.getElementById('{{$area->codigo}}').click()">{{ $area->descripcion }}</label>
        </div>
        @endforeach
  
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>
  
      </form>

    </div>

    
</div>


@endsection
