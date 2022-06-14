@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar Area</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3">

      <form action="{{ route('areas.modificar', $area->codigo) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $area->descripcion }}"></input>
        </div>
        
  
        <button type="submit" class="col-auto btn btn-primary">Guardar cambios</button>
  
      </form>

    </div>

    
</div>


@endsection
