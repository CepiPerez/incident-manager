@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar prioridad</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">

      <form action="{{ route('prioridades.update', $prioridad->id) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $prioridad->descripcion }}">
        </div>

        <div class="row">

          <div class="form-group col-sm">
            <label for="minimo">Valor mínimo</label>
            <input type="number" class="form-control" id="minimo" name="minimo" value="{{ $prioridad->minimo }}">
          </div>
  
          <div class="form-group col-sm">
            <label for="maximo">Valor máximo</label>
            <input type="number" class="form-control" id="maximo" name="maximo" value="{{ $prioridad->maximo }}">
          </div>
  

        </div>
        
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>
  
      </form>

    </div>

    
</div>

@endsection
