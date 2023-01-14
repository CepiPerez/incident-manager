@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container">

    <div class="row mr-0">
        <h3 class="col pt-2">Herramientas</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card">
      <div class="card-body">
        <h5>Resolver incidentes</h5>
        <hr>
        <form action="{{ route('herramientas.resolver') }}" method="GET" class="editor">
          <div class="form-group row m-0 mt-3">
            <label class="col-auto mt-1 p-0" for="fecha_resolucion">Hasta la fecha</label>
            <div class="form-group date con-calendario col-auto m-0" id="datePicker-resolucion" >
              <input type="text" class="form-control texto" placeholder="Seleccione una fecha" 
                name="fecha_resolucion" id="fecha_resolucion" style="text-align:center;">
              <span class="btn fa fa-calendar calendario"></span>
            </div>
          </div>
          <button type="submit" id="guardarCambios" class="col-auto btn btn-outline-slate mt-3">Procesar</button>
        </form>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-body">
        <h5>Cerrar incidentes</h5>
        <hr>
        <form action="{{ route('herramientas.cerrar') }}" method="GET" class="editor">
          <div class="form-group row m-0 mt-3">
            <label class="col-auto mt-1 p-0" for="fecha_cierre">Hasta la fecha</label>
            <div class="form-group date con-calendario col-auto m-0" id="datePicker-cierre" >
              <input type="text" class="form-control texto" placeholder="Seleccione una fecha" 
                name="fecha_cierre" id="fecha_cierre" style="text-align:center;">
              <span class="btn fa fa-calendar calendario"></span>
            </div>
          </div>
          <button type="submit" id="guardarCambios" class="col-auto btn btn-outline-slate mt-3">Procesar</button>
        </form>
      </div>
    </div>


    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datepicker-mod.css') }}">
    <script src="{{ asset('assets/js/datepicker.js') }}"></script>


    <script>

      $(document).ready(function() {
      
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
              
        document.querySelectorAll('.date').forEach(function (element, index)
        {
          var el = element.getAttribute("id");
          $('#'+el).datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd-mm-yyyy'
          });
          $('#'+el).datepicker('setDate', today);
      
        });


      });

    </script>

@endsection
