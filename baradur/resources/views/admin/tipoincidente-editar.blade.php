@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar tipo de incidente</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">

      <form action="{{ route('tipoincidentes.update', $tipo_incidente->codigo) }}" method="post" autocomplete="off">
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

        <div class="form-group col-6 pl-0">
          <label for="formControlRange" id="texto_sla">
          @if ((int)$tipo_incidente->sla==0) Sin SLA definido
          @elseif ((int)$tipo_incidente->sla==1) SLA: 1 hora
          @else SLA {{(int)$tipo_incidente->sla}} horas
          @endif</label>
          <input type="range" id="sla" class="form-control-range" name="sla" 
            min="0" max="72" step="1" value="{{ (int)$tipo_incidente->sla }}">
        </div>

        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>
  
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
