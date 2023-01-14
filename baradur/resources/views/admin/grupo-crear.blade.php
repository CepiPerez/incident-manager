@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear grupo</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('grupos.store') }}" method="post">
        @csrf

        <div class="form-group">
          <label for="descripcion">Nombre del grupo</label>
          <input class="form-control" id="descripcion" name="descripcion" value="{{ $old->descripcion }}" autofocus>
        </div>

        <h5 class="pt-2">Miembros</h5>
        <hr class="mb-3 mt-0">
        <div id="listado" class="bg-slate p-3 mb-0">

            <p id="vacio">No hay miembros registrados</p>
        </div>


        <div class="form-group mt-4">
          <label for="miembro">Agregar miembro</label>
          <div class="row ml-0 mr-2">
            <div class="col m-0 p-0 mr-3">
              <select id="miembro" class="form-control m-0">
                @foreach ($usuarios as $user)
                  <option value="{{$user->idT}}">{{$user->nombre}}</option>
                @endforeach
              </select>
            </div>
            <button class="btn btn-sm btn-outline-slate col-auto ml-0" id="add">Agregar</button>
          </div>
        </div>
    
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar</button>
  
      </form>

    </div>

    
</div>


<script>


  $('#add').on('click', function (e)
  {
    e.preventDefault();

    var exists = false;
    console.log('Selected: '+$('#miembro').val());
    if ($('#miembro').val()==null) return;

    $("input[name='users[]']").each(function() {
      if ($('#miembro').val() == $(this).val())
        exists = true;
    })

    if (exists) return;

    var padre = document.createElement('div');
    padre.classList.add("card");
    padre.classList.add("slate");

    var cont = document.createElement('div');
    cont.classList.add("d-flex");
    cont.setAttribute("style", "padding: 0 1rem;");

    var val1 = document.createElement('span');
    val1.setAttribute("style", "padding: .5rem 1rem;");
    val1.innerHTML = $('#miembro option:selected').text();

    var del = document.createElement('span');
    del.classList.add("borrar_condicion");
    del.classList.add("ri-lg");
    del.classList.add("ri-delete-bin-7-line");

    var input = document.createElement('input');
    input.setAttribute("type", "hidden");
    input.setAttribute("name", "users[]");
    input.setAttribute("value", $('#miembro').val());

    padre.appendChild(val1);
    padre.appendChild(del);
    padre.appendChild(input);

    document.getElementById("listado").appendChild(padre);

    checkEmpty();

  
  });

  $('body').on('click', '.borrar_condicion', function (event) {
    event.target.parentNode.remove();
    checkEmpty();
  });

  function checkEmpty() {
    if (document.getElementById("listado").childElementCount==1)
    {
      $("#vacio").removeAttr("hidden");
    }
    else
    {
      $("#vacio").attr("hidden", true);
    }
  }

</script>

@endsection
