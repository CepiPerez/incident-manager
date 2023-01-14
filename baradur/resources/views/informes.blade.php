@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container">

    <div class="row mr-0">
        <h3 class="col pt-2">Informes</h3>
    </div>
    <hr class="mb-3 mt-0">


    <form action="{{ route('informes.procesar') }}" method="GET" class="editor">

        
        <div class="row">

          @if (Auth::user()->tipo==1)

            <div class="form-group col-md">
              <label for="grupo">Grupo</label>
              <select id="grupo" name="grupo" class="form-control">
                <option value="todos">Todos</option>
                @foreach ($grupos as $grupo)
                  <option value="{{$grupo['codigo']}}">{{$grupo['descripcion']}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md">
              <label for="asignado">Asignado</label>
              <select id="asignado" name="asignado" class="form-control">
                {{-- <option value="todos">Todos</option>
                @foreach ($usuarios as $key => $val)
                  <option value="{{$key}}">{{$val}}</option>
                @endforeach --}}
              </select>
            </div>
    
            <div class="form-group col-md">
              <label for="cliente">Cliente</label>
              <select id="cliente" name="cliente" class="form-control">
                <option value="todos">Todos</option>
                @foreach ($clientes as $item)
                  <option value="{{$item->codigo}}">{{$item->descripcion}}</option>
                @endforeach
              </select>
            </div>

          @else
            
            <input type="hidden" name="grupo" value="todos">
            <input type="hidden" name="asignado" value="todos">
            <input type="hidden" name="cliente" value="{{Auth::user()->cliente}}">

          @endif
    
          
        </div>

        <div class="row mt-1 mb-1">

            <div class="form-group col-md">
              <label for="estado">Estado</label>
              <select id="estado" name="estado" class="form-control">
                <option value="todos">Todos (excluír cancelados)</option>
                <option value="todosinc">Todos (incluír cancelados)</option>
                @foreach ($status as $item)
                  <option value="{{$item->codigo}}">{{$item->descripcion}}</option>
                @endforeach
              </select>
            </div>


            <div class="form-group col-12 col-md-4 m-0">
                <label for="cliente">Desde</label>
                <div class="form-group date con-calendario" id="datePicker-desde" >
                    <input type="text" class="form-control texto" placeholder="Seleccione una fecha" 
                        name="fecha_desde" id="fecha_desde" style="text-align:center;">
                    <span class="btn fa fa-calendar calendario"></span>
                </div>
            </div>

            <div class="form-group col-12 col-md-4 m-0">
                <label for="cliente">Hasta</label>
                <div class="form-group date con-calendario" id="datePicker-hasta" >
                    <input type="text" class="form-control texto" placeholder="Seleccione una fecha" 
                        name="fecha_hasta" id="fecha_hasta" style="text-align:center;">
                    <span class="btn fa fa-calendar calendario"></span>
                </div>
            </div>

        </div>

        <button type="submit" id="guardarCambios" class="col-auto btn btn-outline-slate mt-2">Procesar informe</button>


    </form>

</div>

<script>
    $('#filtrarModal').on('shown.bs.modal', function () {
          $('#filtro_usuario').trigger('focus')
    })

    function filtrar() {
        document.getElementById("filtrarIncidentes").submit();
    }  
</script>

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

  var obj = <?php echo json_encode($usuarios); ?>;
  var arrayUsuarios = Object.values(obj);

  var obj2 = <?php echo json_encode($grupos); ?>;
  var arrayGrupos = Object.values(obj2);

  $('#grupo').on('change', function ()
  {
    var grupo = this.value;

    $("#asignado").children().remove();

    var div = document.createElement('option');
    div.setAttribute('value', "todos");
    div.innerHTML = "Todos";
    document.getElementById("asignado").appendChild(div);

    if (grupo=="todos")
    {
      arrayUsuarios.forEach( function(a)
      {
        var div = document.createElement('option');
        div.setAttribute('value', a.Usuario);
        div.innerHTML = a.nombre;
        document.getElementById("asignado").appendChild(div);
      });
    }
    else
    {
      arrayGrupos.forEach( function(el)
      {
        if (el.codigo==grupo)
        {
          var users = Object.values(el.miembros);
            
          users.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.Usuario);
            div.innerHTML = a.nombre;
            document.getElementById("asignado").appendChild(div);
          });
        }
      });
    }


  });

  $('#grupo').change();


});

</script>

@endsection
