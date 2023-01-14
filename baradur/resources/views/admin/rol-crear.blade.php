@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear Rol</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('roles.store') }}" method="post">
        @csrf

        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $old->descripcion }}" autofocus>
        </div>

        <div class="form-group">
          <label for="tipo">Tipo de usuario</label>
          <select id="tipo" name="tipo" class="form-control">
            <option value=1>Interno</option>
            <option value=0>Externo</option>
          </select>
        </div>

        <h5 class="mt-4">Permisos sobre Incidentes</h5>
        <hr class="mt-0 mb-2">

        @foreach (Permiso::where('id', 1)->get() as $perm)
        <div class="form-check pb-2 ml-0">
          <input type="checkbox" class="form-check-input" name="permisos[]" id="{{$perm->id}}"
            value="{{$perm->id}}">
          <label class="ml-2 form-check-label" style="padding-top:1px;" 
            onclick="document.getElementById('{{$perm->id}}').click()">{{ __('ticketera.'.$perm->descripcion) }}</label>
        </div>
        @endforeach

        @foreach (Permiso::whereBetween('id', [2, 4])->get() as $perm)
        <div class="form-check" style="height:2rem;">
          <input class="form-check-input" type="radio" name="permisos[]" id="{{$perm->id}}"
            value="{{$perm->id}}">
            <label class="ml-2 form-check-input" style="top:-3px;" 
              onclick="document.getElementById('{{$perm->id}}').checked=true">{{ __('ticketera.'.$perm->descripcion) }}</label>
          </label>
        </div>
        @endforeach

        @foreach (Permiso::where('id', '>', 4)->where('id', '<', 8)->get() as $perm)
          <div class="form-check pb-2 ml-0">
            <input type="checkbox" class="form-check-input" name="permisos[]" id="{{$perm->id}}"
              value="{{$perm->id}}">
            <label class="ml-2 form-check-label" style="padding-top:1px;" 
              onclick="document.getElementById('{{$perm->id}}').click()">{{ __('ticketera.'.$perm->descripcion) }}</label>
          </div>
        @endforeach

        <div id="admin">
          
          <h5 class="mt-2">Permisos de Administrador</h5>
          <hr class="mt-0 mb-2">
  
          @foreach (Permiso::where('id', '>', 7)->get() as $perm)
            <div class="form-check pb-2 ml-0">
              <input type="checkbox" class="form-check-input" name="permisosadm[]" id="{{$perm->id}}"
                value="{{$perm->id}}">
              <label class="ml-2 form-check-label" style="padding-top:1px;" 
                onclick="document.getElementById('{{$perm->id}}').click()">{{ __('ticketera.'.$perm->descripcion) }}</label>
            </div>
          @endforeach

        </div>
    
        <button type="submit" class="col-auto btn btn-outline-slate mt-3">Guardar</button>
  
      </form>

    </div>

    
</div>

<script>

  $(document).ready(function()
  {

    $(":checkbox").on('change', function ()
    {

      if (this.value==8) {
        if (this.checked) {
          $('#9').prop('checked', false);
      }}
      if (this.value==9) {
        if (this.checked) {
          $('#8').prop('checked', false);
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

        $("#4").prop('disabled', false);
        if ( $('#4:checked').val() )
          $('#3').prop('checked', true);
      }
      else
      {
        $("#admin").prop('hidden', false);
        
        $("#1").prop('disabled', false);

        $("#4").prop('disabled', true);
      }


    });

    $('#2').prop('checked', true);
    $('#7').prop('checked', true);
    $('#tipo').change();

  });


</script>

@endsection
