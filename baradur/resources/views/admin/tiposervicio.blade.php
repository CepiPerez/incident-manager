@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Tipos de servicio</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('tiposervicio.crear') }}" class="col-auto btn btn-success btn-sm ml-2 mt-2 mb-2 pl-3 pr-3 text-white">
              Agregar tipo de servicio</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col w-auto">Descripcion</th>
            <th scope="col w-auto" class="d-none d-sm-table-cell">Estado</th>
            <th scope="col" style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tipo_servicio as $tipo)
          <tr">
            <td>{{$tipo->codigo}}</td>
            <td>{{$tipo->descripcion}}</td>
            <td class="d-none d-sm-table-cell">{{$tipo->activo==1? 'Activo':'Inactivo'}}</td>
            <td class="text-right no-pointer" style="word-spacing:.75rem;"> 
                <a href="{{ route('tiposervicio.editar', $tipo->codigo) }}" class="fa fa-edit"></a>

                <a href="#" class="fa fa-trash @if($tipo->contador>0) disabled @endif" 
                  @if($tipo->contador==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el tipo de servicio?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('tiposervicio.eliminar', $tipo->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>



@endsection