@extends('layouts.main')

@section('content')


<div class="container mb-4">

    <div class="row">
      <h3 class="col pt-2">Nuevo incidente</h3>
    </div>
    <hr class="mb-3 mt-0">


    <form action="{{ route('incidente.guardar') }}" method="post" enctype="multipart/form-data">
    @csrf

      @if (Auth::user()->cliente==5)
      <div class="row">
        
        <label class="col-auto pt-2 pr-0" for="fecha">Fecha</label>

        <input class="col form-control mr-3 ml-2 mb-3" type="text" id="fecha" 
          name="fecha" autocomplete="false" value="{{ date('d-m-Y H:i') }}"
          style="max-width:160px;">

      </div>
      @endif

      <div class="row">

        <div class="form-group col-sm">
          <label for="cliente">Cliente</label>
          <select id="cliente" name="cliente" @if (Auth::user()->cliente!=5) disabled @endif class="form-control" autofocus>
            @foreach ($cliente as $cli)
              <option value="{{$cli['codigo']}}" @selected($old->cliente==$cli['codigo'])>
                  {{$cli['descripcion']}}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group col-sm">
          <label for="area">Area</label>
          <select id="area" name="area" class="form-control">
          </select>
        </div>

      </div>

      <div class="row">

        <div class="form-group col-sm">
          <label for="tipo_incidente">Tipo de incidente</label>
          <select id="tipo_incidente" name="tipo_incidente" class="form-control">
            @foreach ($tipo_incidente as $ti)
              <option value="{{$ti->codigo}}" @selected($old->tipo_incidente==$ti->codigo)>
                  {{$ti->descripcion}}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group col-sm">
          <label for="modulo">Modulo</label>
          <select id="modulo" name="modulo" class="form-control">
            @foreach ($modulos as $mod)
              <option value="{{$mod->codigo}}" @selected($old->modulo==$mod->codigo)>
                  {{$mod->descripcion}}</option>
            @endforeach
          </select>
        </div>

      </div>

      @if (Auth::user()->cliente==5)
      <div class="row">

        <div class="form-group col-sm">
          <label for="cliente">Estado</label>
          <select id="status" name="status" class="form-control">
            @foreach ($status as $st)
              <option value="{{$st->codigo}}" @selected($old->status==$st->codigo)>
                  {{$st->descripcion}}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group col-sm">
          <label for="cliente">Usuario asignado</label>
          <select id="asignado" name="asignado" class="form-control">
            @foreach ($usuarios as $key => $val)
              <option value="{{$key}}" @selected($old->asignado==$key || Auth::user()->Usuario==$key)>
                  {{$val}}</option>
            @endforeach
          </select>
        </div>

      </div>
      @endif

      <div class="form-group">
        <label for="cliente">Descripcion</label>
        <textarea type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
          rows="5">{{ $old->descripcion }}</textarea>
      </div>

      <div class="form-group">
            <label>Adjuntar archivo</label><br>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFileLang" name="archivo">
                <label class="custom-file-label" data-browse="Seleccionar" for="customFileLang">Seleccionar Archivo</label>
            </div>
      </div>

      <button type="submit" id="guardarCambios" class="col-auto btn btn-primary">Guardar incidente</button>

    </form>



    
</div>

<!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datepicker-mod.css') }}">
<script src="{{ asset('assets/js/datepicker.js') }}"></script> -->

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/newdatetimepicker.css') }}">
<script src="{{ asset('assets/js/newdatetimepicker.js') }}"></script>

<script>


  $('#customFileLang').on('change',function()
  {
    var fileName = $(this).val();
    var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
    $(this).next('.custom-file-label').html(cleanFileName);
  })

  $(document).ready(function(e)
  {

    /*var date = new Date();
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

    }); */

    $('#fecha').datetimepicker({
      format:'d-m-Y H:i',
      formatTime:'H:i',
      formatDate:'d-m-Y',
      step: 10

    });

    var obj = <?php echo json_encode($cliente); ?>;
    var arrayClientes = Object.values(obj);

    $('#cliente').on('change', function ()
    {
      var cliente = this.value;
      //console.log(cliente);
      arrayClientes.forEach( function(el)
      {
        //console.log("cliente" + "::" + el.codigo)
        if (el.codigo==cliente)
        {
          //console.log(el.areas);
          var areas = Object.values(el.areas);
          $("#area").children().remove();

          areas.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.codigo);
            div.innerHTML = a.descripcion;
            document.getElementById("area").appendChild(div);
          });
        }
      });

    });

    $('#cliente').change();

  });


</script>

@endsection
