@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar tipo de avance</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">

      <form action="{{ route('tipoavances.update', $tipo_avance->codigo) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $tipo_avance->descripcion }}">
        </div>
        
        <div class="form-check pb-2 ml-0">
          <input type="checkbox" class="form-check-input" name="visible" id="visible"
            @checked($tipo_avance->visible==1 || $tipo_avance->codigo==30)
            @if($tipo_avance->codigo==30 || $tipo_avance->codigo>100) disabled @endif>
          <label class="ml-2 form-check-label" style="padding-top:2px;" 
            onclick="document.getElementById('visible').click()">Visible para el cliente</label>
        </div>

        <div class="form-check pb-3 ml-0">
          <input type="checkbox" class="form-check-input" name="correo" id="correo"
           @checked($tipo_avance->correo==1)
           @if($tipo_avance->codigo==30 || $tipo_avance->codigo==100) disabled @endif>
          <label class="ml-2 form-check-label" style="padding-top:2px;" 
            onclick="document.getElementById('correo').click()">Enviar notificacion por correo</label>
        </div>

        @if($tipo_avance->codigo!=30 && $tipo_avance->codigo!=100)
          <hr class="mt-0">
          <p class="mb-2">Plantilla de correo</p>
          <textarea class="form-control p-2 mb-3" rows="10" id="html" name="html">{{ $template }}</textarea>
          <div class="card p-2 mb-3 scroll" hidden id="previa" style="min-height:226px;"></div>
          <p class="text-right btn btn-sm btn-outline-secondary mb-3" onClick="cambiarVista()" id="btnvista">Vista previa</p><br>
        @endif

        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>
  
      </form>

    </div>

    
</div>

<script>

  function cambiarVista()
  {
    show = $('#previa').attr('hidden');
    if (!show)
    {
      $('#previa').attr('hidden', true);
      $('#html').attr('hidden', false);
      $('#btnvista').text('Vista previa');
    }
    else
    {
      var text = $('#html').val();
      text = text.replace('$INCIDENTE', '0000123')
        .replace('$REPRESENTANTE_NUEVO', 'Usuario Cualquiera')
        .replace('$REPRESENTANTE', 'Usuario Cualquiera')
        .replace('$DESTINO', 'Usuario Cualquiera')
        .replace('$DESCRIPCION', 'Detalle de la actualización: <br><b>Descripcion de ejemplo de avance del caso</b><br>')
        .replace('$LINK', '/app/incidentes/123/editar');

      $('#previa').html(text);
      $('#previa').attr('hidden', false);
      $('#html').attr('hidden', true);
      $('#btnvista').text('Vista de edicion');
    }

  }
</script>

@endsection
