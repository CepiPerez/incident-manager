@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear sprint</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('periodos.store') }}" method="post">
        @csrf

        <div class="form-group col-12 p-0 ">
          <label for="descripcion">Descripcion (opcional)</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $old->descripcion }}" autofocus>
        </div>

        <div class="form-group col-6 p-0 mr-3">
          <label for="fecha">Fecha de inicio</label>
          <input class="form-control" type="text" id="desde" 
            name="desde" autocomplete="false" value="{{ $old->desde ?? date('d-m-Y') }}"
            style="max-width:130px;">
        </div>

        <div class="form-group col-6 p-0 mr-3">
          <label for="fecha">Fecha de cierre</label>
          <input class="form-control" type="text" id="hasta" 
            name="hasta" autocomplete="hasta" value="{{ $old->hasta ?? date('d-m-Y', now()->addDays(15)->timestamp) }}"
            style="max-width:130px;">
        </div>
    
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar</button>
  
      </form>

    </div>

    
</div>

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/newdatetimepicker.css') }}">
<script src="{{ asset('assets/js/newdatetimepicker.js') }}"></script>

<script>

  $(document).ready(function(e)
  {

    $('#desde').datetimepicker({
      format:'d-m-Y',
      timepicker:false,
      formatDate:'d-m-Y'
    });
  
    $('#hasta').datetimepicker({
      format:'d-m-Y',
      timepicker:false,
      formatDate:'d-m-Y'
    });

  });

</script>

@endsection
