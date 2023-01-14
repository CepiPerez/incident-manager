@extends('layouts.app')

@section('content')


<div class="container" style="margin-top: 16px;">

    <div class="card px-3 pt-2 pb-4">

        <h3 class="pt-2">{{ $title }}</h3>
        <hr class="m-0">

        <p class="mt-3 mb-2 h5">El archivo fue procesado correctamente</p><br>
        <p>Puede proceder a la descarga de los resultados</p>

        @if ($omitidos)
        <div class="card p-2" style="border: 1px solid lightgray; border-left:3px solid red;">
            <p class="text-danger h4 m-2">Registros omitidos:</p>
            @foreach ($omitidos as $item)
                <p class="m-2">{{$item}}</p>
            @endforeach
        </div>
        @endif

        <a href="descarga/{{$descarga}}">
            <button class="btn btn-success mt-3" style="width:160px;">Descargar archivo</button>
        </a>
    </div>

</div>

@endsection
