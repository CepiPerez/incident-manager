@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Informe de incidentes</h3>
        @if ($data->count()>0)
        <div class="col-sm botonera pr-0">
          <a href="{{ route('informes.descargar') }}" class="col-auto btn btn-plain success btn-sm ml-2 mt-3 mb-1">
            <i class="ri-download-cloud-line mr-1" style="vertical-align:middle;"></i>
            Descargar informe
          </a>
        </div>
        @endif
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3 mb-3 info">
      <div class="">Mostrando incidentes desde el {{ $filtros['fecha_desde'] }} hasta el {{ $filtros['fecha_hasta'] }}</div>
            
      @if ($filtros['grupo'])
      <div class="">Grupo asignado: <b>{{ $filtros['grupo_desc'] }}</b></div>
      @endif

      @if ($filtros['asignado'])
      <div class="">Usuario asignado: <b>{{ $filtros['asignado_desc'] }}</b></div>
      @endif

      @if ($filtros['cliente'] && Auth::user()->tipo==1)
      <div class="">Cliente: <b>{{ $filtros['cliente_desc'] }}</b></div>
      @endif

      @if ($filtros['estado'])
      <div class="">Estado: <b>{{ $filtros['estado_desc'] }}</b></div>
      @endif

    </div>

    @if ($data->count()>0)
    <table class="table ticketera">
        <thead>
          <tr>
            <th style="width:6rem;">INC</th>
            <th class="d-none d-xl-table-cell" style="width:120px;">Creado</th>
            @if (!$filtros['cliente'])
            <th class="d-none d-lg-table-cell" style="width:170px;">Cliente</th>
            @endif
            <th class="th-auto">Titulo</th>
            <th style="width:110px;">Estado</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data as $inc)
          <tr>
            <td>{{str_pad($inc->id, 7, '0', STR_PAD_LEFT)}}</td>
            <td class="d-none d-xl-table-cell">{{$inc->fecha_ingreso->format('d-m-Y')}}</td>
            @if (!$filtros['cliente'])
            <td class="d-none d-lg-table-cell">{{$inc->inc_cliente->descripcion}}</td>
            @endif
            <td class="td-truncated">{{$inc->titulo}}</td>
            <td>{{$inc->estado->descripcion}}</td>
          </tr>
          @endforeach
        </tbody>
    </table>
    @else
      <p>No se encontraron incidentes para los filtros indicados</p>

    @endif

    {{ $data->appends(request()->query())->links() }}
    

</div>



@endsection
