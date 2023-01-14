@extends('layouts.main')

@section('content')


<div class="container mb-4 editor">

    <div class="row">
      <h3 class="col pt-2">Nuevo incidente</h3>
    </div>
    <hr class="mb-3 mt-0">


    <form action="{{ route('incidentes.store') }}" method="post" enctype="multipart/form-data">
    @csrf

      <div class="row m-0">

        @if (Auth::user()->tipo==1)
        <div class="form-group col-auto p-0 mr-3">
          <label for="fecha">Fecha</label>
          <input class="form-control" type="text" id="fecha" 
            name="fecha" autocomplete="false" value="{{ date('d-m-Y H:i') }}"
            style="max-width:130px;">
        </div>
        @endif

        @if (Auth::user()->tipo==1)
        <div class="form-group col-sm p-0">
          <label for="cliente">Cliente</label>
          <select id="cliente" name="cliente" @if (Auth::user()->tipo==0) disabled @endif class="form-control" autofocus>
            <option value="null" @selected($old->cliente=="null")>Seleccione</option>
            @foreach ($cliente as $cli)
              <option value="{{$cli['codigo']}}" @selected($old->cliente==$cli['codigo'])>
                  {{$cli['descripcion']}}</option>
            @endforeach
          </select>
        </div>
        @else
         <input type="hidden" id="cliente" name="cliente" value="{{Auth::user()->cliente}}">
        @endif

      </div>


      <div class="row m-0">
        
        @if (Auth::user()->tipo==1)
        <div class="form-group col-md p-0 mr-0 mr-md-3">
          <label for="remitente">Remitente</label>
          <select id="remitente" name="remitente" class="form-control">
            <option value="null" @selected($old->cliente=="null")>(sin remitente)</option>
            {{-- @foreach ($cliente as $cli)
              <option value="{{$cli['codigo']}}" @selected($old->cliente==$cli['codigo'])>
                  {{$cli['descripcion']}}</option>
            @endforeach --}}
          </select>
        </div>
        @endif

        <div class="form-group col-md p-0">
          <label for="area">Area</label>
          <select id="area" name="area" class="form-control">
            <option value="null" @selected($old->area=="null")>Seleccione</option>
          </select>
        </div>

      </div>

      <div class="row m-0">

        <div class="form-group col-md p-0 mr-0 mr-md-3">
          <label for="tipo_incidente">Tipo de incidente</label>
          <select id="tipo_incidente" name="tipo_incidente" class="form-control">
            <option value="null" @selected($old->tipo_incidente=="null")>Seleccione</option>
            @foreach ($tipo_incidente as $ti)
              <option value="{{$ti->codigo}}" @selected($old->tipo_incidente==$ti->codigo)>
                  {{$ti->descripcion}}</option>
            @endforeach
          </select>
        </div>
        
        <div class="form-group col-md p-0">
          <label for="modulo">Modulo</label>
          <select id="modulo" name="modulo" class="form-control">
            <option value="null" @selected($old->modulo=="null")>Seleccione</option>
            @foreach ($modulos as $mod)
              <option value="{{$mod->codigo}}" @selected($old->modulo==$mod->codigo)>
                  {{$mod->descripcion}}</option>
            @endforeach
          </select>
        </div>

      </div>

      {{-- <div class="form-group col-sm p-0">
        <label for="cliente">Estado</label>
        <select id="status" name="status" class="form-control">
          <option value="null" @selected($old->status=="null")>Seleccione</option>
          @foreach ($status as $st)
            <option value="{{$st->codigo}}" @selected($old->status==$st->codigo)>
                {{$st->descripcion}}</option>
          @endforeach
        </select>
      </div> --}}

      @if (Auth::user()->tipo==1)
      <div class="row m-0">

        <div class="form-group col-md p-0 mr-0 mr-md-3">
          <label for="cliente">Grupo asignado</label>
          <select id="grupo" name="grupo" class="form-control">
            <option value="null" @selected($old->grupo=="null")>(sin asignar)</option>
            @foreach ($grupos as $grupo)
              <option value="{{$grupo['codigo']}}" 
                @selected($old->grupo==$grupo['codigo'] || Auth::user()->grupos->first()->codigo==$grupo['codigo'])>
                {{$grupo['descripcion']}}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group col-md p-0">
          <label for="cliente">Usuario asignado</label>
          <select id="asignado" name="asignado" class="form-control">
            <option value="null" @selected($old->asignado=="null")>(sin asignar)</option>
            {{-- @foreach ($usuarios as $key => $val)
              <option value="{{$key}}" @selected($old->asignado==$key || Auth::user()->Usuario==$key)>
                  {{$val}}</option>
            @endforeach --}}
          </select>
        </div>

      </div>
      @endif

      <div class="form-group">
        <label for="titulo">Titulo</label>
        <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" 
          value="{{ $old->titulo }}">
      </div>

      <div class="form-group">
        <label for="descripcion">Descripcion</label>
        <textarea type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
          id="inc_desc" oninput="auto_grow(this)" 
          {{-- rows="5" --}}>{{ $old->descripcion }}</textarea>
      </div>

      {{-- <div class="form-group">
        <label>Adjuntar archivo</label><br>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFileLang" name="archivo">
            <label class="custom-file-label" data-browse="Seleccionar" for="customFileLang">Seleccionar Archivo</label>
        </div>
      </div> --}}

      <div class="form-group">
        <label>Archivos adjuntos</label>
        <div class="attachemnts-card p-2">
          <button class="btn btn-sm btn-plain slate upload-btn ml-1" type="button">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar archivo
          </button>
          <input name="archivo[]" type="file" class="file d-none" multiple 
            accept=".csv, .xls, .xlsx, .doc, .docx, .png, .jpeg, .jpg, .txt, .zip, .rar, .csv, .pdf"/>
          <div class="image-container"></div>
        </div>
      </div>


      <button type="submit" id="guardarCambios" class="col-auto btn btn-outline-slate mt-3">Guardar incidente</button>

    </form>


    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" hidden 
      data-delay="5000" style="position:absolute;top:1rem;right:1rem;opacity:1;">
      <div class="toast-header bg-danger" style="height:1rem;">
      </div>
      <div class="toast-body">
        <div class="row">
          <div class="col-auto pt-1 mr-3" id="toast-list">
            <li>El incidente se encuentra incompleto</li>
          </div>
          <div class="col">
            <button type="button" class="ml-auto mb-1 close" data-dismiss="toast" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    
</div>

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/newdatetimepicker.css') }}">
<script src="{{ asset('assets/js/newdatetimepicker.js') }}"></script>

<link href="{{ asset('assets/css/imageupload.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/js/imageupload.js') }}"></script>

<script>

  /* $('#customFileLang').on('change',function()
  {
    var fileName = $(this).val();
    var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
    $(this).next('.custom-file-label').html(cleanFileName);
  }) */

  function auto_grow(element) {
      element.style.height = "5px";
      element.style.height = (element.scrollHeight)+"px";
  }
  

  $(document).ready(function(e)
  {

    auto_grow(document.getElementById('inc_desc'));

    window.addEventListener('resize', function(event){
      auto_grow(document.getElementById('inc_desc'));
    });

    $('#fecha').datetimepicker({
      format:'d-m-Y H:i',
      formatTime:'H:i',
      formatDate:'d-m-Y',
      step: 10

    });

    var obj = <?php echo json_encode($cliente); ?>;
    var arrayClientes = Object.values(obj);

    var obj2 = <?php echo json_encode($grupos); ?>;
    var arrayGrupos = Object.values(obj2);

    var currentUser = '{{ Auth::user()->Usuario }}';

    var start = true;

    $('#cliente').on('change', function ()
    {
      var cliente = this.value;
      //console.log(cliente);
      arrayClientes.forEach( function(el)
      {
        //console.log("cliente" + "::" + el.codigo)
        if (el.codigo==cliente)
        {
          var areas = Object.values(el.areas);
          $("#area").children().remove();

          var div = document.createElement('option');
          div.setAttribute('value', "null");
          div.innerHTML = "Seleccione";
          document.getElementById("area").appendChild(div);

          areas.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.codigo);
            div.innerHTML = a.descripcion;
            document.getElementById("area").appendChild(div);
          });


          var usuarios = Object.values(el.usuarios);
          $("#remitente").children().remove();

          var div = document.createElement('option');
          div.setAttribute('value', "null");
          div.innerHTML = "(sin remitente)";
          document.getElementById("remitente").appendChild(div);

          usuarios.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.Usuario);
            div.innerHTML = a.nombre;
            document.getElementById("remitente").appendChild(div);
          });

        }
      });

    });

    $('#cliente').change();

    $('#grupo').on('change', function ()
    {
      var grupo = this.value;
      console.log("GRUPO: "+grupo)

      if (grupo=="null")
      {
          $("#asignado").children().remove();
          var div = document.createElement('option');
          div.setAttribute('value', "null");
          div.innerHTML = "(sin asignar)";
          document.getElementById("asignado").appendChild(div);
      }
      else
      {
        arrayGrupos.forEach( function(el)
        {
          if (el.codigo==grupo)
          {
            var users = Object.values(el.miembros);
            $("#asignado").children().remove();
  
            var div = document.createElement('option');
            div.setAttribute('value', "null");
            div.innerHTML = "(sin asignar)";
            document.getElementById("asignado").appendChild(div);
  
            users.forEach( function(a)
            {
              var div = document.createElement('option');
              div.setAttribute('value', a.Usuario);
  
              if (a.Usuario==currentUser && start)
                div.setAttribute('selected', true);
  
              div.innerHTML = a.nombre;
              document.getElementById("asignado").appendChild(div);
            });
          }
        });
      }


    });

    $('#grupo').change();

    start = false;


    $('form').on('submit', function() {

      /* console.log('CLIENTE: ' +$('#cliente').val());
      console.log('AREA: ' +$('#area').val());
      console.log('MODULE: ' +$('#modulo').val());
      console.log('PROBLEM: ' +$('#tipo_incidente').val());
      console.log('ASSIGNED: ' +$('#asignado').val());
      console.log('title: ' +$('#titulo').val());
      console.log('description: ' +$('#inc_desc').val()); */

      if ($('#area').val()=="null" || $('#modulo').val()=="null" 
        || $('#cliente').val()=="null" || $('#tipo_incidente').val()=="null"
        || $('#titulo').val()=='' || $('#inc_desc').val()=='')
      {
        $('.toast').prop('hidden', false);
        $('.toast').toast('show');        
        return false;
      }

    });

  });


</script>

@endsection
