@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar módulo</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">

      <form action="{{ route('modulos.update', $modulo->codigo) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $modulo->descripcion }}"></input>
        </div>
        
        <div class="form-group col-6 pl-0">
          <label for="formControlRange" id="texto_prioridad">Prioridad: {{ (int)$modulo->pondera }}</label>
          <input type="range" id="prioridad" class="form-control-range" name="prioridad" 
            min="0" max="100" step="5" value="{{ (int)$modulo->pondera }}">
        </div>

        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>
  
      </form>

    </div>

    
</div>

<script>

  var slider = document.getElementById("prioridad");
  var output = document.getElementById("texto_prioridad");
  //output.innerHTML = 'Prioridad: ' slider.value;

  // Update the current slider value (each time you drag the slider handle)
  slider.oninput = function() {
    output.innerHTML = 'Prioridad: ' + this.value;
  }

</script>

@endsection
