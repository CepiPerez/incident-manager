@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Tipos de servicio</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('tiposervicios.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
            <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar tipo de servicio</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-sm-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th class="d-none d-md-table-cell text-center" style="width:7rem;">Prioridad</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tipo_servicio as $tipo)
          <tr>
            <td class="d-none d-sm-table-cell" >{{$tipo->codigo}}</td>
            <td>{{$tipo->descripcion}}</td>
            <td class="d-none d-md-table-cell text-center">{{(int)$tipo->pondera}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('tiposervicios.edit', $tipo->codigo) }}" class="ri-lg ri-edit-line"></a>

                <a href="#" class="ri-lg ri-delete-bin-7-line @if($tipo->clientes_count>0) disabled @endif" 
                  @if($tipo->clientes_count==0)
                    onclick="window.confirm('Esta seguro que desea eliminar el tipo de servicio?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('tiposervicios.destroy', $tipo->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                  @endif
                ></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $tipo_servicio->appends(request()->query())->links(true) }}

    <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form>

</div>



@endsection