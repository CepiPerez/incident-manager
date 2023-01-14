@extends('layouts.main')

@section('content')


<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Perfil de usuario</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="">

      <h4 class="">{{ $usuario->Usuario }}</h4>
      <hr class="mt-0">

      <form action="{{ route('usuarios.perfil.modificar', $usuario->idT) }}" 
        method="post" autocomplete="off" enctype="multipart/form-data">
        @csrf
        @method('put')

        <div class="row">

          <div class="col-md">
            
            <div class="form-group">
              <label for="nombre">Nombre Completo</label>
              <input class="form-control" id="nombre" name="nombre" value="{{ $usuario->nombre }}">
            </div>
    
            <div class="form-group">
              <label for="clave">Contraseña (ingresar si desea cambiarla)</label>
              <input class="form-control" id="clave" name="clave" type="password" value="">
            </div>
    
            <div class="form-group">
              <label for="email">Email</label>
              <input class="form-control" id="email" name="email" value="{{ $usuario->Mail }}" autocomplete="false">
            </div>

          </div>

          <div class="col-md text-left text-md-center">

            <div class="form-group">
              <label for="nombre">Imagen de perfil</label><br>
              <img src="{{ $usuario->avatar }}" alt="" height="164" width="164" class="profilepic edit" id="profilepic">
              {{-- <i class="ri-edit-box-line profilepic-edit"></i> --}}
              <input hidden type="file" class="custom-file-input" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.webp">
            </div>


          </div>

        </div>

  
  
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar cambios</button>
  
      </form>

    </div>

    
</div>

<script>

    $('#profilepic').on('click',function() {
      $('#avatar').click();  
    })

    $('#avatar').on('change',function() {
      imgPreview(this);
    })

    function imgPreview(input){
     if(input.files && input.files[0]){
       var reader = new FileReader();
       reader.onload = function(e){
         $("#profilepic").show().attr("src", e.target.result);
       }
       reader.readAsDataURL(input.files[0]);
     }
    }


</script>

@endsection
