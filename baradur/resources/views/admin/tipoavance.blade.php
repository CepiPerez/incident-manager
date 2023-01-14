@extends('layouts.main')

@section('content')

<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Avances</h3>
    </div>
    <hr class="mb-3 mt-0">

    <table class="table">
        <thead>
          <tr>
            <th class="d-none d-sm-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tipo_avance as $tipo)
          <tr>
            <td class="d-none d-sm-table-cell" >{{$tipo->codigo}}</td>
            <td>{{$tipo->descripcion}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('tipoavances.edit', (int)$tipo->codigo) }}" class="ri-lg ri-edit-line"></a>
            </td>
          </tr>
          @endforeach
        </tbody>
    </table>

    {{ $tipo_avance->appends(request()->query())->links(true) }}

</div>

@endsection