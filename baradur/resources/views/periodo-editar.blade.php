@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

  <div class="row mr-0">
      <h3 class="col-sm pt-2">Detalle del Sprint</h3>
  </div>


  <form action="{{ route('periodos.update', $periodo->codigo) }}" method="post">
    @method('put')
    @csrf

    <div class="form-group col-12 p-0 ">
      <input class="form-control" id="descripcion" placeholder="Descripcion" name="descripcion" value="{{ $periodo->descripcion }}">
    </div>

    
    <div class="row m-0">
      <div class="form-group col-6 p-0">
        <label for="fecha">Fecha de inicio</label>
        <input class="form-control" type="text" id="desde" 
          name="desde" autocomplete="false" value="{{ Carbon::parse($periodo->desde)->format('d-m-Y') }}"
          style="max-width:130px;">
      </div>

      <div class="form-group col-6 p-0">
        <label for="fecha">Fecha de cierre</label>
        <input class="form-control" type="text" id="hasta" 
          name="hasta" autocomplete="hasta" value="{{ Carbon::parse($periodo->hasta)->format('d-m-Y') }}"
          style="max-width:130px;">
      </div>
    </div>
      
    <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>

  </form>

  <form id="form-delete" method="post" action="" class="d-none">
    @csrf 
    @method('delete')
  </form>


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