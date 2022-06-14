@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar tipo de incidente</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3">

      <form action="{{ route('tipoincidente.modificar', $tipo_incidente->codigo) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $tipo_incidente->descripcion }}"></input>
        </div>
        
        <div class="form-group col-6 pl-0">
          <label for="formControlRange" id="texto_prioridad">Prioridad: {{ (int)$tipo_incidente->pondera }}</label>
          <input type="range" id="prioridad" class="form-control-range" name="prioridad" 
            min="0" max="100" step="5" value="{{ (int)$tipo_incidente->pondera }}">
        </div>

        <button type="submit" class="col-auto btn btn-primary">Guardar cambios</button>
  
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
