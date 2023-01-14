@extends('layouts.main')

@section('content')


<div class="container pb-3 ">

    <div class="row">
      <h3 class="col pt-2">
        Incidente {{ str_pad($data->id, 7, '0', STR_PAD_LEFT) }}
      </h3>
        @if ($data->status<10 && $data->status!=5 && Auth::user()->tipo==1)
          <p class="col-auto text-secondary" style="height:2rem;padding-top:16px;padding-right:1.25rem;">
            @php $s = Utils::sla_expiration($data->horas, $data->sla); @endphp
            @if ($s['expired']) 
              <span class="text-danger"> 
              <i class="ri-error-warning-line" style="vertical-align:middle;" aria-hidden="true"></i>
            @elseif ($s['hours'] < 3)
              <span class="text-orange"> 
              <i class="ri-error-warning-line" style="vertical-align:middle;" aria-hidden="true"></i>
            @endif
            {{ $s['text'] }}
            @if ($s['expired'] || $s['hours'] < 3) 
              </span>
            @endif
          </p>
        @endif

    </div>
    <hr class="mb-3 mt-0">

    <form action="{{ route('incidentes.update', $data->id) }}" method="post" id="guardarCambios">
      @csrf
      @method('put')


      <div class="row m-0 p-0 editor">


        <!-- Panel Principal -->
        <div class="col-12 col-lg-9 m-0 p-0 pr-0 pr-lg-4 principal">

          <div class="form-group col-sm p-0">
            <input type="text" id="titulo" name="titulo" class="form-control" autofocus 
            value="{{ $data->titulo}}"
            @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>
          </div>

          <div class="form-group">
            <label for="descripcion">Descripcion</label>
            <textarea type="text" name="descripcion" class="form-control" {{-- rows="6" --}} id="inc_desc" oninput="auto_grow(this)"
            @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>{{ $data->descripcion }}</textarea>
          </div>

          @if (count($data->adjunto)>0)
              <label>Archivos adjuntos</label><br>
              @foreach ($data->adjunto as $adjunto)
                <a href="{{ route('incidente.descargar.adjunto', (int)$data->id, 0, $adjunto->adjunto) }}"
                  style="text-decoration: none;">
                  @if ( Utils::check_img(Storage::path('archivos/'.(int)$data->id.'/0/'.$adjunto->adjunto)) )
                    <div class="btn btn-outline-secondary attachment img mb-2 p-0">
                      <img src="{{asset('public/archivos/'.(int)$data->id.'/0/'.$adjunto->adjunto)}}" alt="" class="incident-attachment">
                      <span class="incident-attachment-text">{{ $adjunto->adjunto }}</span>
                    </div>
                  @else
                    <div class="btn btn-outline-secondary pl-3 pr-3 p-0 mb-2 grid attachment" {{-- style="height:2rem;" --}}>
                    <img src="{{ Utils::get_icon_svg($adjunto->adjunto) }}" alt="" class="pt-2 m-1" height=42 width=42><br>
                    {{ $adjunto->adjunto }}
                    </div>
                  @endif
                </a>
              @endforeach
          @endif

          <div class="row m-0 mt-2">

            <div class="form-group col-md p-0 mr-0 mr-md-3">
              <label for="cliente">Cliente</label>
              <select id="cliente" name="cliente" class="form-control" autofocus 
                @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>
                @foreach ($cliente as $cli)
                  <option value="{{$cli['codigo']}}" @selected($data->inc_cliente->descripcion==$cli['descripcion'])>
                      {{$cli['descripcion']}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md p-0">
              <label for="area">Area</label>
              <select id="area" name="area" class="form-control" 
                @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>
              </select>
            </div>

          </div>

          <div class="row m-0">
            
            <div class="form-group col-md p-0 mr-0 mr-md-3">
              <label for="tipo_incidente">Tipo de incidente</label>
              <select id="tipo_incidente" name="tipo_incidente" class="form-control" 
                @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>
                @foreach ($tipo_incidente as $ti)
                  <option value="{{$ti->codigo}}" @selected($data->tipo_incidente->descripcion==$ti->descripcion)>
                      {{$ti->descripcion}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md p-0">
              <label for="modulo">Modulo</label>
              <select id="modulo" name="modulo" class="form-control" 
                @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>
                @foreach ($modulos as $mod)
                  <option value="{{$mod->codigo}}" @selected($data->modulo==$mod->codigo)>
                      {{$mod->descripcion}}</option>
                @endforeach
              </select>
            </div>
            
          </div>


          <label>Avances / Notas</label>

          <div class="bg-slate pt-3 pr-3 pl-3 pb-1">

            <!-- Avances del incidente -->
            @if (count($data->avances)>0)
            @foreach ($data->avances as $value)
              @if (Auth::user()->tipo==1 || $value->visible==1)
              <div class="card card-body m-0 pt-1 pl-1 pr-1 pb-0 mb-2
                  @if ($value->tipo_avance==100) yellow
                  @elseif ($value->tipo_avance==30) orange
                  @else slate
                  @endif">
                  <div class="m-0">
                    <div class="row m-0 pl-2 pr-2 pt-1" 
                      @if ($value->descripcion ||
                          count($value->adjuntos)>0 ||
                          (($value->usuario==Auth::user()->Usuario || Auth::user()->rol==1) && $data->status<20)
                        )
                        data-toggle="collapse" href="#collapse_{{$loop->index}}" role="button" aria-expanded="false" aria-controls="collapse_{{$loop->index}}"
                      @endif
                      >
                        <div class="col-6 p-0 text-left">
                          <p class="text-strong m-0 mb-2">
                          @if ($value->tipo_avance==2)
                            Derivado a {{ $value->grupo_desc }}
                            @if ($value->destino)
                             > {{ $usuarios->find('Usuario', $value->destino)->nombre }}
                            @endif
                          @else
                            {{ $value->tipo_desc }}
                          @endif
                          </p>
                        </div>
                        <div class="col-6 p-0 text-right text-secondary">
                          <span class="d-none d-md-inline">
                            <img src="{{ $usuarios->find('Usuario', $value->usuario)->avatar }}" alt=""
                               class="profilepic small">
                            {{ $usuarios->find('Usuario', $value->usuario)->nombre }} 
                          </span>
                          <span class="m-0 mb-2 ml-3">
                            <i class="ri-calendar-line pr-1" style="vertical-align:middle;"></i>
                            {{ substr($value->fecha_ingreso, 0, 10) }}
                          </span>
                        </div>
                    </div>

                    @if ($value->descripcion ||
                      $value->adjuntos ||
                      (($value->usuario==Auth::user()->Usuario || Auth::user()->rol==1) && $data->status<20)
                    )

                    <div class="collapse" id="collapse_{{$loop->index}}">

                      @if ($value->descripcion)
                        <div class="row text-small ml-0 mr-2 mb-2">
                          <p class="pl-2 pr-2 m-0 pt-0 text-secondary">{{ htmlentities($value->descripcion) }}</p>
                        </div>
                      @endif

                      @if ($value->adjuntos)
                        <div class="d-flex text-small ml-2 mr-2 mb-0 pb-0">
                        @foreach ($value->adjuntos as $adjunto)
                            <a href="{{ route('incidente.descargar.adjunto', $adjunto->incidente, $adjunto->avance, $adjunto->adjunto) }}"
                              target="_blank" style="text-decoration: none;">
                              @if ( Utils::check_img(Storage::path('archivos/'.$adjunto->incidente.'/'.$adjunto->avance.'/'.$adjunto->adjunto)) )
                                <div class="btn btn-outline-secondary attachment img mb-2 p-0">
                                  <img src="{{asset('public/archivos/'.$adjunto->incidente.'/'.$adjunto->avance.'/'.$adjunto->adjunto)}}" alt="" class="incident-attachment">
                                  <span class="incident-attachment-text">{{ $adjunto->adjunto }}</span>
                                </div>
                              @else
                                <div class="btn btn-outline-secondary pr-3 pt-0 mb-2 grid attachment">
                                <img src="{{ Utils::get_icon_svg($adjunto->adjunto) }}" alt="" class="pt-2 m-1" height=42 width=42><br>
                                {{ $adjunto->adjunto }}
                                </div>
                              @endif
                            </a>
                        @endforeach
                        </div>
                      @endif

                      @if (($value->usuario==Auth::user()->Usuario || Auth::user()->rol==1) && $data->status<20)
                        <div class="col text-right text-small m-0 p-0 mb-2 pr-2 pb-1">
                          <div class="col-auto text-right p-0 pl-0 m-0 mb-0 text-dark"> 
                            <a href="#" onclick="eliminarAvance('{{route('incidente.avance.eliminar', $data->id, $value->id) }}')"
                              class="btn btn-sm btn-plain danger m-0 pl-2 pr-2 pt-0 pb-0">
                              <i class="ri-xl ri-delete-bin-7-line mr-2 m-0 p-0" style="font-size:.75rem;"></i>Eliminar
                            </a> 
                          </div>
                        </div>
                      @endif

                    </div>

                    @endif

                  </div>
              </div>
              @endif
            @endforeach
            @else
              <p>No hay avances registrados</p>
            @endif

            <div class="botonera p-0 m-0 mt-3">
              @if(Auth::user()->tipo==1 && $data->status<20)
                <span class="col-auto btn btn-plain btn-sm orange mb-2 p-0 mr-3" 
                  {{-- data-toggle="modal" data-target="#agregarNota" --}} onclick="agregarNota()">
                  <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar nota privada
                </span>
                <span class="col-auto btn btn-plain btn-sm slate mb-2 p-0 ml-1" 
                  {{-- data-toggle="modal" data-target="#agregarAvance" --}} onclick="agregarAvance()">
                  <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar avance
                </span>
              @elseif (Auth::user()->tipo==0 && $data->status<10)
                <span class="col-auto btn btn-plain btn-sm slate mb-2 p-0 ml-1" 
                  {{-- data-toggle="modal" data-target="#agregarNota"> --}} onclick="agregarNotaUser()">
                  <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar nota
                </span>
              @endif
            </div>

          </div>


          <div class="separador"></div>
          
        </div>


        <!-- Panel Lateral -->
        <div class="col-none col-lg-3 pl-0 pl-lg-4 mt-3 mt-lg-0 pr-0">

          <div class="row m-0 p-0">

            <div class="col-6 col-lg-12 form-group m-0 p-0">
              <label for="cliente">Creado</label>
              <input class="col form-control" type="text" id="fecha" 
                @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif
                name="fecha" autocomplete="false" value="{{ $data->fecha_ingreso->rawFormat('d-m-Y H:i') }}"
                style="max-width:130px;">
            </div>
            {{-- @if ($data->remitente != $data->usuario)
            <p class="text-secondary mt-2"><small> Por {{ $usuarios->where('Usuario', $data->usuario)->first()->nombre }}<small></p>
            @endif --}}
  
            <div class="col-6 col-lg-12 form-group m-0 pl-0 pl-md-2 pl-lg-0 pr-0 mt-lg-4">
              <label>Remitente</label>
              <br>
              <div class="d-flex mt-0 mt-lg-0">
                <img src="{{ $usuarios->find('Usuario', $data->remitente)->avatar }}" alt="" 
                  class="profilepic mt-1 mr-2 d-none d-md-block">
                @if (Auth::user()->tipo==0)
                <span style="font-size: 1.1rem;">
                  {{ $usuarios->find('Usuario', $data->remitente)->nombre }}
                </span>
                @else
                  <select id="remitente" name="remitente" class="form-control remitente"  
                  @if(Auth::user()->tipo==0 || $data->status>=20) disabled @endif>
                  @foreach ($remitentes as $rte)
                    <option value="{{$rte['Usuario']}}" @selected($data->remitente==$rte['Usuario'])>
                        {{$rte['nombre']}}</option>
                  @endforeach
                @endif
              </select>
              </div>
            </div>

          </div>

          <div class="row m-0 p-0 mt-3">

            <div class="col-6 col-lg-12 form-group pt-2 pl-0">
              <label for="cliente">Estado</label><br>
              <i class="badge @if ($data->status==0) badge-orange
                  @elseif ($data->status==5) badge-teal
                  @elseif ($data->status==10) badge-green
                  @elseif ($data->status==20) badge-gray
                  @elseif ($data->status==50) badge-lightgray
                  @else badge-blue
                  @endif">{{ $data->estado->descripcion }}</i>
            </div>

            <div class="col-6 col-lg-12 form-group pt-2 pl-0 pl-md-2 pl-lg-0">
              <label>Grupo asignado</label>
              <br>
              @if ($data->grupo)
                <img src="{{ Storage::url('profile/group.png') }}" alt="" class="profilepic">
                <span style="font-size: 1.1rem;">{{ $data->grupo_desc }}</span>
              @else
                <img src="{{ Storage::url('profile/no_group.png') }}" alt="" class="profilepic">
                <span style="font-size: 1.1rem;opacity:.7;">Sin asignar</span>
              @endif
            </div>

          </div>

          <div class="row m-0 p-0">
    
            <div class="col-6 col-lg-12 form-group pt-2 pl-0">
              <label>Usuario asignado</label>
              <br>
              @if ($data->status!=0 && $data->asignado)
                  <img src="{{ $usuarios->find('Usuario', $data->asignado)->avatar }}" alt="" class="profilepic">
                  <span style="font-size: 1.1rem;">{{ $usuarios->find('Usuario', $data->asignado)->nombre }}</span>
              @else
                  <img src="{{ Storage::url('profile/unassigned.png') }}" alt="" class="profilepic">
                  <span style="font-size: 1.1rem;opacity:.7;">Sin asignar</span>
                  {{-- <p class="mt-1 mb-0"><a href="">Tomar el incidente</a></p>
                  <p class="m-0"><a href="">Asignar a usuario</a></p> --}}
              @endif
            </div>

            <div class="col-6 col-lg-12 form-group pt-2 pl-0 pl-md-2 pl-lg-0">
              <label for="cliente">Prioridad</label>
              <br>
              <img src="{{asset('assets/icons/'.$data->pid.'.svg')}}" alt="" class="priority mr-2">
              <span style="font-size: 1.1rem;">{{$data->pdesc}}</span>
            </div>

          </div>
          

          @if (Auth::user()->tipo==1 && $data->status<20)
      
            <hr class="mt-0 mt-lg-5">
            <div class="row m-0">
                <button type="submit" class="btn btn-outline-slate">Guardar cambios</button>
            </div>

          @elseif ($data->status < 20)

            <hr class="mt-5">
            <div class="row m-0">
              @if($data->status==10)
                <span class="btn btn-outline-slate mb-2" onclick="reabrirIncidente()">
                  Reabrir incidente
                </span>
              @endif
              <span class="btn btn-outline-slate mr-2" onclick="cerrarIncidente()">
                Cerrar incidente
              </span>
              @if($data->avances->whereNotContains('tipo_avance', 30)->count()==0)
                <span class="btn btn-outline-secondary mt-2" onclick="cancelarIncidente()">
                  Cancelar incidente
                </button>
              @endif
            </div>
            
          @endif
            
        </div>
            
        
  
      </div>
    </form>
  


    <!-- Modal agregar avance -->
    {{-- @if (Auth::user()->tipo==1) --}}
    <div class="modal fade" id="agregarAvance" tabindex="-1" role="dialog" aria-labelledby="agregarAvanceLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="agregarAvanceLabel">Agregar avance</h5>
            </div>
            <div class="editor" style="margin:15px;">
                <form action="{{ route('incidente.avance.guardar', $data->id) }}" method="post" id="guardarAvance" enctype="multipart/form-data">
                  @csrf

                    <div class="form-group" id="selectorAvance">
                    <label for="tipo_avance">Tipo de avance</label>
                    <select class="form-control" id="tipo_avance" name="tipo_avance">
                        @foreach ($tipo_avance as $ava)
                          @if ($ava->codigo!=30 && $ava->codigo!=100)
                          <option value="{{$ava->codigo}}">{{$ava->descripcion}}</option>
                          @endif
                        @endforeach
                    </select>
                    </div>

                    <input type="hidden" id="tipo_avance_sub" name="tipo_avance_sub">

                    <div class="text-danger mb-4" id="nota_danger" hidden>
                      ATENCION: Una vez guardado el estado seleccionado, el mismo no se podrá volver a cambiar y el incidente ya no podrá ser modificado.
                    </div>

                    <div id="asignacion" hidden>
                      <div class="form-group">
                        <label for="grupo">Grupo asignado</label>
                        <select class="form-control" id="grupo" name="grupo">
                            @foreach ($grupos as $g)
                            <option value="{{$g['codigo']}}" @selected($data->grupo? $data->grupo==$g['codigo'] : 
                              Auth::user()->grupos()->first()->codigo==$g['codigo'])>{{$g['descripcion']}}</option>
                            @endforeach
                        </select>
                      </div> 
                      <div class="form-group">
                      <label for="usuario">Usuario asignado</label>
                      <select class="form-control" id="usuario" name="usuario">
                          {{-- @foreach ($usuarios as $u)
                          <option value="{{$u->Usuario}}" @selected($data->asignado? $data->asignado==$u->Usuario : Auth::user()->Usuario==$u->Usuario)>{{$u->nombre}}</option>
                          @endforeach --}}
                      </select>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="descripcion">Descripcion</label>
                      <textarea type="text" name="descripcion" id="av_descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                        rows="3"></textarea>
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
                    

                </form>
                <button onclick="guardarAvance()" class="btn btn-outline-slate">Guardar</button>
            </div>
        </div>
      </div>
    </div>
    {{-- @endif --}}

    
</div>

<form action="" method="post" id="eliminarAvance">
  @csrf
  @method('delete')
</form>


<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/newdatetimepicker.css') }}">
<script src="{{ asset('assets/js/newdatetimepicker.js') }}"></script>

<link href="{{ asset('assets/css/imageupload.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/js/imageupload.js') }}"></script>

<script>


  function agregarAvance() {
    $('#agregarAvanceLabel').text('Agregar avance');
    $('.modal-header').removeClass('bg-danger');
    $('.modal-header').removeClass('bg-warning');
    $('#selectorAvance').attr('hidden', false);
    $('#tipo_avance_sub').val('avance');
    $('#agregarAvance').modal('toggle');
  }

  function agregarNota() {
    $('#agregarAvanceLabel').text('Agregar nota privada');
    $('.modal-header').removeClass('bg-danger');
    $('.modal-header').addClass('bg-warning');
    $('#selectorAvance').attr('hidden', true);
    $('#tipo_avance_sub').val('nota');
    $('#tipo_avance').val('00001').change();
    $('#agregarAvance').modal('toggle');
  }

  function agregarNotaUser() {
    $('#agregarAvanceLabel').text('Agregar nota');
    $('.modal-header').removeClass('bg-danger');
    $('.modal-header').removeClass('bg-warning');
    $('#selectorAvance').attr('hidden', true);
    $('#tipo_avance_sub').val('nota');
    $('#tipo_avance').val('00001').change();
    $('#agregarAvance').modal('toggle');
  }

  function cancelarIncidente() {
    $('#agregarAvanceLabel').text('Cancelar incidente');
    $('.modal-header').removeClass('bg-warning');
    $('.modal-header').addClass('bg-danger');
    $('#selectorAvance').attr('hidden', true);
    $('#nota_danger').attr('hidden', false);
    $('#nota_danger').text('ATENCION: Una vez cerrado el incidente, el mismo ya no podrá ser modificado.');
    $('#tipo_avance_sub').val('cancelar');
    $('#tipo_avance').val('00050').change();
    $('#agregarAvance').modal('toggle');
  }

  function cerrarIncidente() {
    $('#agregarAvanceLabel').text('Cerrar incidente');
    $('.modal-header').removeClass('bg-warning');
    $('.modal-header').addClass('bg-danger');
    $('#selectorAvance').attr('hidden', true);
    $('#nota_danger').attr('hidden', false);
    $('#nota_danger').text('ATENCION: Una vez cerrado el incidente, el mismo ya no podrá ser modificado.');
    $('#tipo_avance_sub').val('cerrar');
    $('#tipo_avance').val('00020').change();
    $('#agregarAvance').modal('toggle');
  }

  function reabrirIncidente() {
    $('#agregarAvanceLabel').text('Reapertura del incidente');
    $('.modal-header').removeClass('bg-warning');
    $('.modal-header').removeClass('bg-danger');
    $('#selectorAvance').attr('hidden', true);
    $('#nota_danger').attr('hidden', true);
    $('#tipo_avance_sub').val('reabrir');
    $('#tipo_avance').val('00001').change();
    $('#agregarAvance').modal('toggle');
  }

  /* $('#customFileLang').on('change',function() {
      var fileName = $(this).val();
      var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
      $(this).next('.custom-file-label').html(cleanFileName);
  })

  $('#customFileLang2').on('change',function() {
      var fileName = $(this).val();
      var cleanFileName = fileName.replace('C:\\fakepath\\', " ");
      $(this).next('.custom-file-label').html(cleanFileName);
  }) */

  $('#tipo_avance').on('change',function() {
      if($(this).val() >= 20)
        $('#nota_danger').attr('hidden', false);
      else
        $('#nota_danger').attr('hidden', true);

      if($(this).val()==2)
        $('#asignacion').attr('hidden', false);
      else
        $('#asignacion').attr('hidden', true);


  })

  $('#agregarAvance').on('shown.bs.modal', function () {
    if ($('#selectorAvance').attr('hidden'))
      $('#av_descripcion').trigger('focus')
    else
      $('#tipo_avance').trigger('focus')
  })

  /* $('#agregarNota').on('shown.bs.modal', function () {
        $('#d_nota').trigger('focus')
  }) */

  function guardarIncidente() {
      document.getElementById("guardarCambios").submit();
  } 

  function guardarAvance() {
      document.getElementById("guardarAvance").submit();
  }

  /* function cierreCliente() {
      document.getElementById("cierreCliente").submit();
  }

  function cancelCliente() {
      document.getElementById("cancelCliente").submit();
  } */

  function eliminarAvance(url) {
    //console.log("ELIMINAR: " +url);
    $('#eliminarAvance').attr('action', url);
    $('#eliminarAvance').submit();
  }

  /* function guardarNota() {
      document.getElementById("guardarNota").submit();
  } */

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

    var obj3 = <?php echo json_encode($usuarios->where('tipo', 1)->where('activo', 1)->toArray()); ?>;
    var arrayUsuarios = Object.values(obj3);

    var area_actual = {{ $data->area }};
    var rte_actual = '{{ $data->remitente }}';
    //console.log("AREA: "+area_actual);

    $('#cliente').on('change', function ()
    {
      var cliente = this.value;
      //console.log(cliente);
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

          var usuarios = Object.values(el.usuarios);
          $("#remitente").children().remove();

          usuarios.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.Usuario);
            if (a.Usuario==rte_actual)
              div.setAttribute('selected', true);
            div.innerHTML = a.nombre;
            document.getElementById("remitente").appendChild(div);
          });

          arrayUsuarios.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.Usuario);
            if (a.Usuario==rte_actual)
              div.setAttribute('selected', true);
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
      arrayGrupos.forEach( function(el)
      {
        if (el.codigo==grupo)
        {
          var users = Object.values(el.miembros);
          $("#usuario").children().remove();

          var div = document.createElement('option');
          div.setAttribute('value', "null");
          div.innerHTML = "(sin asignar)";
          document.getElementById("usuario").appendChild(div);

          users.forEach( function(a)
          {
            var div = document.createElement('option');
            div.setAttribute('value', a.Usuario);
            div.innerHTML = a.nombre;
            document.getElementById("usuario").appendChild(div);
          });
        }
      });

    });

    $('#grupo').change();
    
  });

</script>


@endsection
