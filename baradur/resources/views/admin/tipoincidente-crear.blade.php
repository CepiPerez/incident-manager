@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear tipo de incidente</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('tipoincidentes.store') }}" method="post">
        @csrf

        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $old->descripcion }}" autofocus></input>
        </div>

        <div class="form-group col-6 pl-0">
          <label for="formControlRange" id="texto_prioridad">Prioridad: {{ (int)$old->prioridad }}</label>
          <input type="range" id="prioridad" class="form-control-range" name="prioridad" 
            min="0" max="100" step="5" value="{{ (int)$old->prioridad }}">
        </div>

        <div class="form-group col-6 pl-0">
          <label for="formControlRange" id="texto_sla">
            Sin SLA definido
          </label>
          <input type="range" id="sla" class="form-control-range" name="sla" 
            min="0" max="72" step="1" value="{{ (int)$old->sla }}">
        </div>
    
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar</button>
  
      </form>

    </div>

    
</div>

<script>

  var slider = document.getElementById("prioridad");
  var output = document.getElementById("texto_prioridad");
  slider.oninput = function() {
    output.innerHTML = 'Prioridad: ' + this.value;
  }

  var slider2 = document.getElementById("sla");
  var output2 = document.getElementById("texto_sla");
  slider2.oninput = function() {
    if (this.value==0)
      output2.innerHTML = 'Sin SLA definido';
    else if (this.value==1)
      output2.innerHTML = 'SLA: 1 hora';
    else
      output2.innerHTML = 'SLA: ' + this.value + ' horas';
  }
  

</script>

@endsection
