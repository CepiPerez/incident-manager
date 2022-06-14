@extends('layouts.main')

@section('content')


<div class="container">

    <h3 class="pt-2">Detalle de incidente #{{ $data->id }}</h3>
    <hr class="mb-3">

    <div class="row">

      <div class="form-group col-6">
        <label for="fecha">Fecha</label>
        <input type="text" class="form-control" readonly value="{{ $data->fecha }}">
      </div>
  
      <div class="form-group col-6">
        <label for="cliente">Cliente</label>
        <input type="text" class="form-control" readonly value="{{ $data->cliente->descripcion }}">
      </div>

    </div>

    <div class="row">

      <div class="form-group col-6">
        <label for="cliente">Tipo de incidente</label>
        <input type="text" class="form-control" readonly value="{{ $data->tipo_incidente->descripcion }}">
      </div>

      <div class="form-group col-6">
        <label for="cliente">Estado</label>
        <input type="text" class="form-control" readonly value="{{ $data->status->descripcion }}">
      </div>

    </div>

    <div class="form-group">
      <label for="cliente">Descripcion</label>
      <textarea type="text" class="form-control" rows="13" readonly>{{ $data->descripcion }}</textarea>
    </div>


    
</div>


@endsection
