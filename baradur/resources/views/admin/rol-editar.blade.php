@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Modificar Rol</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">

      <form action="{{ route('roles.update', $rol->id) }}" method="post" autocomplete="off">
        @csrf
        @method('put')
  
        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $rol->descripcion }}">
        </div>

        <div class="form-group">
          <label for="tipo">Tipo de usuario</label>
          <select id="tipo" name="tipo" class="form-control">
            <option value=1 @selected($rol->tipo==1)>Interno</option>
            <option value=0 @selected($rol->tipo==0)>Externo</option>
          </select>
        </div>

        <h5 class="mt-4">Permisos de usuario</h5>
        <hr class="mt-0 mb-2">

        @foreach (Permiso::where('id', 1)->get() as $perm)
        <div class="form-check pb-2 ml-0">
          <input type="checkbox" class="form-check-input" name="permisos[]" id="{{$perm->id}}"
            value="{{$perm->id}}" @checked( in_array($perm->id, $permisos_rol) )>
          <label class="ml-2 form-check-label" style="padding-top:1px;" 
            onclick="elementClick('{{$perm->id}}')">{{ __('ticketera.'.$perm->descripcion) }}</label>
        </div>
        @endforeach

        @foreach (Permiso::whereBetween('id', [2, 4])->get() as $perm)
        <div class="form-check" style="height:2rem;">
          <input class="form-check-input" type="radio" name="permisos[]" id="{{$perm->id}}"
            value="{{$perm->id}}" @checked( in_array($perm->id, $permisos_rol) )>
            <label class="ml-2 form-check-input" style="top:-3px;" 
              onclick="elementClick('{{$perm->id}}')">{{ __('ticketera.'.$perm->descripcion) }}</label>
          </label>
        </div>
        @endforeach

        @foreach (Permiso::where('id', '>', 4)->where('id', '<', 50)->get() as $perm)
          <div class="form-check pb-2 ml-0">
            <input type="checkbox" class="form-check-input" name="permisos[]" id="{{$perm->id}}"
              value="{{$perm->id}}" @checked( in_array($perm->id, $permisos_rol) )>
            <label class="ml-2 form-check-label" style="padding-top:1px;" 
              onclick="elementClick('{{$perm->id}}')">{{ __('ticketera.'.$perm->descripcion) }}</label>
          </div>
        @endforeach

        <div id="admin">

          <h5 class="mt-4">Permisos de Usuarios Internos</h5>
          <hr class="mt-0 mb-2">

          @foreach (Permiso::whereBetween('id', array(50, 60))->get() as $perm)
            <div class="form-check pb-2 ml-0">
              <input type="checkbox" class="form-check-input" name="permisosadm[]" id="{{$perm->id}}"
                value="{{$perm->id}}" @checked( in_array($perm->id, $permisos_rol) )>
              <label class="ml-2 form-check-label" style="padding-top:1px;" 
                onclick="elementClick('{{$perm->id}}')">{{ __('ticketera.'.$perm->descripcion) }}</label>
            </div>
          @endforeach

          {{-- @foreach (Permiso::whereBetween('id', [53, 55])->get() as $perm)
          <div class="form-check" style="height:2rem;">
            <input class="form-check-input" type="radio" name="permisosadm[]" id="{{$perm->id}}"
              value="{{$perm->id}}" @checked( in_array($perm->id, $permisos_rol) )>
              <label class="ml-2 form-check-input" style="top:-3px;" 
                onclick="elementClick('{{$perm->id}}')">{{ __('ticketera.'.$perm->descripcion) }}</label>
            </label>
          </div>
          @endforeach --}}


          <h5 class="mt-4">Permisos de Administrador</h5>
          <hr class="mt-0 mb-2">

          @foreach (Permiso::where('id', '>', 100)->get() as $perm)
            <div class="form-check pb-2 ml-0">
              <input type="checkbox" class="form-check-input" name="permisosadm[]" id="{{$perm->id}}"
                value="{{$perm->id}}" @checked( in_array($perm->id, $permisos_rol) )>
              <label class="ml-2 form-check-label" style="padding-top:1px;" 
                onclick="elementClick('{{$perm->id}}')">{{ __('ticketera.'.$perm->descripcion) }}</label>
            </div>
          @endforeach

        </div>
  
        <button type="submit" class="col-auto btn btn-outline-slate mt-3">Guardar cambios</button>
  
      </form>

    </div>

    
</div>

<script>

  function elementClick(element) {
   
    el = document.getElementById(element);

    console.log("ELEMENT: "+element);

    if (!el.disabled) el.click();
  }

  $(document).ready(function()
  {

    $(":checkbox").on('change', function ()
    {

      if (this.value==101) {
        if (this.checked) {
          $('#102').prop('checked', false);
      }}
      
      if (this.value==102) {
        if (this.checked) {
          $('#101').prop('checked', false);
      }}

      if (this.value==50) {
        if (!this.checked) {
          $('#51').prop('checked', false);
          $('#52').prop('checked', false);
        }
      }

      if (this.value==51) {
        if (this.checked) {
          $('#50').prop('checked', true);
      }}

      if (this.value==52) {
        if (this.checked) {
          $('#50').prop('checked', true);
      }}

      if (this.value==53) {
        if (this.checked) {
          $('#50').prop('checked', true);
      }}

    });


    $('#tipo').on('change', function ()
    {
      
      if (this.value==0)
      {
        $("#admin").prop('hidden', true);

        $("#2").prop('disabled', true);
        if ( $('#2:checked').val() )
          $('#3').prop('checked', true);

        //$("#4").prop('disabled', false);

        $("#5").prop('disabled', true);
        $('#5').prop('checked', false);

        $("#8").prop('disabled', true);
        $('#8').prop('checked', false);
      }
      else
      {
        $("#admin").prop('hidden', false);

        $("#2").prop('disabled', false);

        //$("#4").prop('disabled', true);
        //if ( $('#4:checked').val() )
        //  $('#3').prop('checked', true);

        $("#5").prop('disabled', false);

        $("#8").prop('disabled', false);
      }

    });

    $('#tipo').change();

  });


</script>


@endsection
