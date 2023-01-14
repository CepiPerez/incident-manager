@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear Area</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('areas.store') }}" method="post">
        @csrf

        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $old->descripcion }}" autofocus></input>
        </div>
    
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar</button>
  
      </form>

    </div>

    
</div>


@endsection
