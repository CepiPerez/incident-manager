@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Informe de incidentes</h3>
        <div class="col-sm botonera pr-0">
          <a href="{{ route('informes.descargar') }}" class="col-auto btn btn-success btn-sm ml-2 mt-2 mb-2 pl-3 pr-3 text-white">
            Descargar informe</a>
        </div>
    </div>
    <hr class="mb-3 mt-0">

    <div class="card p-3 mb-3">
      <div class="">Mostrando incidentes desde el {{ $filtros['fecha_desde'] }} hasta el {{ $filtros['fecha_hasta'] }}</div>
      
      @if ($filtros['cliente'])
      <div class="">Cliente: <b>{{ $filtros['cliente'] }}</b></div>
      @endif
      
      @if ($filtros['asignado'])
      <div class="">Usuario asignado: <b>{{ $filtros['asignado'] }}</b></div>
      @endif

      @if ($filtros['estado'])
      <div class="">Estado: <b>{{ $filtros['estado'] }}</b></div>
      @endif

    </div>

    @if (count($data)>0)
    <table class="table">
        <thead>
          <tr>
            <th scope="col">INC</th>
            <th scope="col w-auto">Fecha</th>
            <th scope="col w-auto">Cliente</th>
            <th scope="col w-auto">Tipo</th>
            <th scope="col w-auto">Estado</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data as $inc)
          <tr>
            <td>{{$inc->id}}</td>
            <td>{{$inc->fecha_ingreso}}</td>
            <td>{{$inc->cliente->descripcion}}</td>
            <td>{{$inc->tipo_incidente->descripcion}}</td>
            <td>{{$inc->status->descripcion}}</td>
          </tr>
          @endforeach
        </tbody>
    </table>
    @else
      <p>No se encontraron incidentes para los filtros indicados</p>

    @endif

    {{ $data->appends(request()->query())->links(true) }}
    

</div>



@endsection
