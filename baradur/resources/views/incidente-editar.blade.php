@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Incidente #{{ $data->id }}</h3>
      <p class="col-auto text-secondary pt-3" style="height:2rem;">
        Creado por <b>{{ $data->usuario }}</b> el {{ $data->fecha }}</p>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3">

      <form action="{{ route('incidente.modificar', $data->id) }}" method="post" id="guardarCambios">
        @csrf
        @method('put')

        <div class="row">
        
          <label class="col-auto pt-2 pr-0" for="fecha">Fecha</label>

          <input class="col form-control mr-3 ml-2 mb-3" type="text" id="fecha" 
            @if(Auth::user()->cliente!=5 || $data->status->codigo>=20) disabled @endif
            name="fecha" autocomplete="false" value="{{ date('d-m-Y H:i', strtotime($data->fecha_ingreso)) }}"
            style="max-width:160px;">

        </div>
  
        <div class="row">
  
          <div class="form-group col-sm">
            <label for="cliente">Cliente</label>
            <select id="cliente" name="cliente" class="form-control" autofocus 
              @if(Auth::user()->cliente!=5 || $data->status->codigo>=20) disabled @endif>
              @foreach ($cliente as $cli)
                <option value="{{$cli['codigo']}}" @selected($data->cliente->descripcion==$cli['descripcion'])>
                    {{$cli['descripcion']}}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group col-sm">
            <label for="area">Area</label>
            <select id="area" name="area" class="form-control" 
              @if(Auth::user()->cliente!=5 || $data->status->codigo>=20) disabled @endif>
            </select>
          </div>

        </div>

        <div class="row">
  
          <div class="form-group col-sm">
            <label for="tipo_incidente">Tipo de incidente</label>
            <select id="tipo_incidente" name="tipo_incidente" class="form-control" 
              @if(Auth::user()->cliente!=5 || $data->status->codigo>=20) disabled @endif>
              @foreach ($tipo_incidente as $ti)
                <option value="{{$ti->codigo}}" @selected($data->tipo_incidente->descripcion==$ti->descripcion)>
                    {{$ti->descripcion}}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group col-sm">
            <label for="modulo">Modulo</label>
            <select id="modulo" name="modulo" class="form-control" 
              @if(Auth::user()->cliente!=5 || $data->status->codigo>=20) disabled @endif>
              @foreach ($modulos as $mod)
                <option value="{{$mod->codigo}}" @selected($data->modulo==$mod->codigo)>
                    {{$mod->descripcion}}</option>
              @endforeach
            </select>
          </div>
  
        </div>
  
        <div class="row">
  
          <div class="form-group col-sm">
            <label for="cliente">Estado</label>
            <input readonly class="form-control" value="{{ $data->status->descripcion }}"></input>
          </div>
  
          <div class="form-group col-sm">
            <label for="cliente">Usuario asignado</label>
            <input readonly class="form-control" value="{{ $data->status->descripcion=='Sin Asignar'? '' : $usuarios[$data->asignado] }}"></input>
          </div>
  
        </div>
  
        <div class="form-group">
          <label for="cliente">Descripcion</label>
          <textarea type="text" name="descripcion" class="form-control" rows="5" 
          @if(Auth::user()->cliente!=5 || $data->status->codigo>=20) disabled @endif>{{ $data->descripcion }}</textarea>
        </div>

        <div class="row ml-1 mr-1">

          @if ($data->adjunto)
          <div class="col m-0 p-0">
            <p class="pt-1 pl-0 pr-3">Archivo adjunto:</p>
            <a href="{{ asset('public/archivos/'.$data->adjunto->incidente.'/'.$data->adjunto->avance.'/'.$data->adjunto->adjunto) }}"
              class="btn btn-sm btn-secondary pr-3 text-white pt-0" style="height:2rem;">
              <i class="fa fa-paperclip mr-2 ml-1" style="font-size:1rem;padding-top:6px;"></i>
              {{ $data->adjunto->adjunto }}
            </a> 
          </div>
          @endif

          @if(Auth::user()->cliente==5 && $data->status->codigo<20)
          <div class="col p-0 text-right">
            <button type="submit" class="col-auto btn btn-primary">Guardar cambios</button>
          </div>
          @endif

        </div>
  
  
      </form>

    </div>

    
    <div class="row mr-0 mt-4">
      <h5 class="col-lg mt-1 mb-1">Avances / Notas</h5>

      <div class="col-lg botonera pr-0 pl-3">
        @if(Auth::user()->cliente==5 && $data->status->codigo<20)
          <button class="col-auto btn btn-warning btn-sm mb-1 pl-3 pr-3 mr-2" 
            data-toggle="modal" data-target="#agregarNota" >
              Agregar nota privada
          </button>
          <button class="col-auto btn btn-success btn-sm mb-1 pl-3 pr-3" 
            data-toggle="modal" data-target="#agregarAvance" >
              Agregar avance
          </button>
        @elseif (Auth::user()->cliente!=5)
          <button class="col-auto btn btn-info btn-sm mb-1 pl-3 pr-3" 
            data-toggle="modal" data-target="#agregarNota">
              Agregar nota
          </button>
        @endif
      </div>
    </div>
    <hr class="mt-1 mb-3">
    @forelse ($data->avances as $value)
      <div class="card card-body m-0 pt-1 pl-1 pr-1 pb-0 mb-2" style="border-left: .5rem solid 
          @if ($value->tipo_avance==100) #ffc107
          @elseif ($value->tipo_avance==30) #fd7e14
          @else #6bb9de
          @endif">
          <div class="m-0">
              <div class="row m-0 pl-2 pr-2 pt-1">
                  <div class="col-6 p-0 text-left"><p class="text-strong m-0 mb-2">{{ $tipo_avance->where('codigo', $value->tipo_avance)->shift()->descripcion }}</p></div>
                  <div class="col-6 p-0 text-right text-secondary">
                    <span class="d-none d-md-inline">
                      <i class="fa fa-user pr-1" style="font-size:.75rem;position:relative;top:-1px;"></i>
                      {{ $usuarios[$value->usuario] }} 
                    </span>
                    <span class="m-0 mb-2 ml-3">
                      <i class="fa fa-calendar pr-1" style="font-size:.75rem;position:relative;top:-1px;"></i>
                      {{ substr($value->fecha_ingreso, 0, 10) }}
                    </span>
                  </div>
              </div>
              <div class="row text-secondary text-small ml-0 mr-2 mb-2">
                  <p class="col-auto pl-2 pr-2 m-0 pt-0 text-truncate">{{ $value->descripcion }}</p>
                  @if ($value->adjunto)
                  <div class="col text-right p-0 m-0"> 
                    <a href="{{ route('incidente.descargar.adjunto', $value->adjunto->incidente, $value->adjunto->avance, $value->adjunto->adjunto) }}"
                    class="btn btn-sm btn-outline-secondary m-0 pl-2 pr-2 pt-0" style="height:1.85rem;">
                    <i class="fa fa-paperclip mr-2 m-0 p-0" style="font-size:.75rem;"></i>{{ $value->adjunto->adjunto }}</a> 
                  </div>
                  @endif
              </div>
          </div>
      </div>
    @empty
    <p>No hay avances registrados</p>
    @endforelse

    <!-- Modal agregar avance -->
    <div class="modal fade" id="agregarAvance" tabindex="-1" role="dialog" aria-labelledby="agregarAvanceLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarAvanceLabel">Agregar avance</h5>
            </div>
            <div style="margin:15px;">
                <form action="{{ route('incidente.avance.guardar', $data->id) }}" method="post" id="guardarAvance" enctype="multipart/form-data">
                  @csrf

                    <div class="form-group">
                    <label for="tipo_avance">Tipo de avance</label>
                    <select class="form-control" id="tipo_avance" name="tipo_avance">
                        @foreach ($tipo_avance as $ava)
                          @if ($ava->codigo!=30 && $ava->codigo!=100)
                          <option value="{{$ava->codigo}}">{{$ava->descripcion}}</option>
                          @endif
                        @endforeach
                    </select>
                    </div>  

                    <div class="text-danger mb-4" id="nota_danger" hidden>
                      ATENCION: Una vez guardado el estado seleccionado, el mismo no se podrá volver a cambiar y el incidente ya no podrá ser modificado.
                    </div>

                    <div class="form-group">
                    <label for="usuario">Usuario asignado</label>
                    <select class="form-control" id="usuario" name="usuario">
                        @foreach ($usuarios as $key => $val)
                        <option value="{{$key}}" @selected($data->asignado? $data->asignado==$key : Auth::user()->Usuario==$key)>{{$val}}</option>
                        @endforeach
                    </select>
                    </div> 
                    
                    <div class="form-group">
                      <label for="descripcion">Descripcion</label>
                      <textarea type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                        rows="3"></textarea>
                    </div>

                    <div class="form-group">
                      <label>Adjuntar archivo</label><br>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFileLang" name="archivo">
                        <label class="custom-file-label" data-browse="Seleccionar" for="customFileLang">Seleccionar Archivo</label>
                      </div>
                    </div>

                </form>
                <button onclick="guardarAvance()" class="btn btn-success">Guardar</button>
            </div>
        </div>
      </div>
    </div>

    <!-- Modal agregar nota privada -->
    <div class="modal fade" id="agregarNota" tabindex="-1" role="dialog" aria-labelledby="agregarNotaLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarNotaLabel">Agregar nota privada</h5>
            </div>
            <div style="margin:15px;">
                <form action="{{ route('incidente.nota.guardar', $data->id) }}" method="post" id="guardarNota" enctype="multipart/form-data">
                  @csrf
                    
                    <div class="form-group">
                      <label for="descripcion">Descripcion</label>
                      <textarea type="text" name="descripcion_nota" id="d_nota" class="form-control @error('descripcion_nota') is-invalid @enderror"
                        rows="3"></textarea>
                    </div>

                    <div class="form-group">
                      <label>Adjuntar archivo</label><br>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFileLang2" name="archivonota">
                        <label class="custom-file-label" data-browse="Seleccionar" for="customFileLang2">Seleccionar Archivo</label>
                      </div>
                    </div>

                </form>
                <button onclick="guardarNota()" class="btn btn-success">Guardar</button>
            </div>
        </div>
      </div>
    </div>

    
</div>

<!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datepicker-mod.css') }}">
<script src="{{ asset('assets/js/datepicker.js') }}"></script> -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/newdatetimepicker.css') }}">
<script src="{{ asset('assets/js/newdatetimepicker.js') }}"></script>


<script>
    $('#customFileLang').on('change',function() {
        var fileName = $(this).val();
        var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
        $(this).next('.custom-file-label').html(cleanFileName);
    })

    $('#customFileLang2').on('change',function() {
        var fileName = $(this).val();
        var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
        $(this).next('.custom-file-label').html(cleanFileName);
    })

    $('#tipo_avance').on('change',function() {
        if($(this).val() >= 20)
          $('#nota_danger').attr('hidden', false);
        else
          $('#nota_danger').attr('hidden', true);
    })


    $('#agregarAvance').on('shown.bs.modal', function () {
          $('#tipo_avance').trigger('focus')
    })

    $('#agregarNota').on('shown.bs.modal', function () {
          $('#d_nota').trigger('focus')
    })

    function guardarIncidente() {
        document.getElementById("guardarCambios").submit();
    } 

    function guardarAvance() {
        document.getElementById("guardarAvance").submit();
    }

    function guardarNota() {
        document.getElementById("guardarNota").submit();
    }

    $(document).ready(function(e)
    {

      $('#fecha').datetimepicker({
        format:'d-m-Y H:i',
        formatTime:'H:i',
        formatDate:'d-m-Y',
        step: 10

      });

      var obj = <?php echo json_encode($cliente); ?>;
      var arrayClientes = Object.values(obj);

      var area_actual = {{ $data->area }};
      //console.log("AREA: "+area_actual);

      $('#cliente').on('change', function ()
      {
        var cliente = this.value;
        console.log(cliente);
        arrayClientes.forEach( function(el)
        {
          //console.log(cliente + "::" + el.codigo)
          if (el.codigo==cliente)
          {
            //console.log(el.areas);
            var areas = Object.values(el.areas);
            $("#area").children().remove();

            areas.forEach( function(a)
            {
              var div = document.createElement('option');
              div.setAttribute('value', a.codigo);
              if (a.codigo==area_actual)
                div.setAttribute('selected', true);
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
