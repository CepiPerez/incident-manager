@extends('layouts.main')

@section('content')

<div class="container pb-3">

    <div class="row">
      <h3 class="col pt-2">Crear regla de asignación</h3>
    </div>
    <hr class="mb-3 mt-0">

    <div class="editor">


      <form action="{{ route('asignaciones.store') }}" method="post">
        @csrf

        <div class="form-group">
          <label for="descripcion">Descripcion</label>
          <input class="form-control" id="descripcion" name="descripcion" 
          value="{{ $old->descripcion }}" autofocus>
        </div>

        <label>Condiciones</label>
        <div class="bg-slate p-3">
          <div id="listado">
          </div>
          <p id="vacio">No hay condiciones</p>

          <div class="botonera p-0 m-0 mt-3">
            <span class="col-auto btn btn-plain btn-sm slate mb-1 p-0 mr-2" 
              data-toggle="modal" data-target="#agregarCondicion" id="btn_agregar">
              <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar condición
            </span>
          </div>

        </div>

        <div class="row mt-3">

          <div class="form-group col-md pr-3 pr-md-1">
            <label for="grupo">Grupo</label>
            <select id="grupo" name="grupo" class="form-control">
              @foreach ($grupos as $gr)
                <option value="{{$gr['codigo']}}" @selected($old->grupo==$gr['codigo'])>
                  {{ $gr['descripcion'] }}</option>
              @endforeach
            </select>
          </div>
  
          <div class="form-group col-md">
            <label for="asignado">Usuario</label>
            <select id="asignado" name="asignado" class="form-control">
            </select>
          </div>

        </div>
        
  
        <button type="submit" class="col-auto btn btn-outline-slate mt-2">Guardar</button>
  
      </form>

    </div>

    <!-- Modal add condition -->
    <div class="modal fade" id="agregarCondicion" tabindex="-1" role="dialog" aria-labelledby="agregarCondicionLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="agregarCondicion">Agregar condición</h5>
            </div>
            <div class="editor" style="margin:15px;">
                <form action="" id="agregaCondicion">
                  
                  <div class="form-group">
                    <label for="valor">Campo a comprobar</label>
                    <select id="valor" name="valor" class="form-control">
                      <option value='cliente'>Cliente</option>
                      <option value='area'>Area</option>
                      <option value='modulo'>Módulo</option>
                      <option value='tipo_incidente'>Tipo de incidente</option>
                    </select>
                  </div>
                  
                  <div class="form-group" id="comparador">

                    <label for="valor">Igual a</label>
                    <select id="seleccion" name="seleccion" class="form-control">
                    </select>

                  </div>

                </form>
                <button onclick="agregaCondicion()" class="btn btn-outline-slate mt-2">Agregar</button>
            </div>
        </div>
      </div>
    </div>
    
</div>


<script>

  var obj2 = <?php echo json_encode($grupos); ?>;
  var arrayGrupos = Object.values(obj2);
  
  $('#valor').on('change', function ()
  {
    $("#seleccion").children().remove();
    
    var obj = undefined;

    if (this.value=='cliente')
      obj = <?php echo json_encode($clientes); ?>;

    else if (this.value=='area')
      obj = <?php echo json_encode($areas); ?>;

    else if (this.value=='modulo')
      obj = <?php echo json_encode($modulos); ?>;

    else if (this.value=='tipo_incidente')
      obj = <?php echo json_encode($tipo_incidentes); ?>;
    

    keysSorted = Object.keys(obj).sort(function(a, b) {
      return obj[a].localeCompare(obj[b])
    });

    for (var valor of keysSorted) {
      //console.log("Valor: " + obj[valor]);
      var div = document.createElement('option');
      div.setAttribute('value', valor);
      div.innerHTML = obj[valor];
      document.getElementById("seleccion").appendChild(div);
    }

  });


  $('#grupo').on('change', function ()
  {
    var grupo = this.value;

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
          div.innerHTML = a.nombre;
          document.getElementById("asignado").appendChild(div);
        });
      }
    });

  });

  $(document).ready(function(e)
  {
    $('#grupo').change();
  });
  
  $('#btn_agregar').on('click', function ()
  {
    document.getElementById('agregaCondicion').reset();
    $('#valor').change();

  });

  $('body').on('click', '.borrar_condicion', function (event) {
    event.target.parentNode.remove();
    checkEmpty();
  });
  
  function agregaCondicion()
  {
    valor_val = $("#valor option:selected").val();
    valor_text = $("#valor option:selected").text();
    seleccion = $("#seleccion option:selected").val()
    texto = $("#seleccion option:selected").text()

    //console.log(valor_val + "::" + valor_text + "::" + seleccion + ":::" + texto);

    if (valor_val!=undefined && seleccion!=undefined)
      crearCondicion(valor_text, valor_val, seleccion, texto)

  }

  function crearCondicion(valor_text, valor_val, seleccion, texto)
  {

    var padre = document.createElement('div');
    padre.classList.add("card");
    padre.classList.add("slate");
  
    var cont = document.createElement('div');
    cont.classList.add("d-flex");
    cont.setAttribute("style", "padding: .5rem 1rem;");

    var val1 = document.createElement('span');
    val1.classList.add("pr-3");
    val1.innerHTML = '<b>' + valor_text + '</b>';

    var val2 = document.createElement('span');
    val2.innerHTML = texto;

    cont.appendChild(val1);
    cont.appendChild(val2);

    var val1i = document.createElement('input');
    val1i.setAttribute("type", "hidden");
    val1i.setAttribute("name", "conditions[]");
    val1i.setAttribute("value", valor_val);

    var val2i = document.createElement('input');
    val2i.setAttribute("type", "hidden");
    val2i.setAttribute("name", "values[]");
    val2i.setAttribute("value", seleccion);

    var val3i = document.createElement('input');
    val3i.setAttribute("type", "hidden");
    val3i.setAttribute("name", "text[]");
    val3i.setAttribute("value", texto);

    cont.appendChild(val1i);
    cont.appendChild(val2i);
    cont.appendChild(val3i);

    padre.appendChild(cont);

    var del = document.createElement('span');
    del.classList.add("borrar_condicion");
    del.classList.add("ri-lg");
    del.classList.add("ri-delete-bin-7-line");

    padre.appendChild(del);

    document.getElementById("listado").appendChild(padre);

    checkEmpty();

    $('#agregarCondicion').modal('toggle');

  }

  function checkEmpty() {
    if (document.getElementById("listado").childElementCount==0)
    {
      $("#vacio").removeAttr("hidden");
    }
    else
    {
      $("#vacio").attr("hidden", true);
    }
  }

  $(document).ready(function () {
    $('#valor').change();
    $('#group_id').change();
  });

  $('form').on('submit', function() {

    if ($('#descripcion').val()=='' || document.getElementById("listado").childElementCount==0)
    {
      $('.toast').prop('hidden', false);
      $('.toast').toast('show');        
      return false;
    }


  });


</script>
@endsection

