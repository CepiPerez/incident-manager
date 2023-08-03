@extends('layouts.main')

@section('content')

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col-sm pt-2">Calendario</h3>
        <div class="col-sm botonera pr-0">
          @can('crear_periodos')
            <a href="{{ route('periodos.create') }}" class="col-auto btn btn-plain slate btn-sm ml-2 mt-3 mb-1">
              <i class="ri-add-line mr-2 m-0 p-0" style="vertical-align:middle;"></i>Agregar sprint</a>
          @endcan
          <a href="{{ route('periodos.show', 0) }}" class="col-auto btn btn-plain success btn-sm ml-4 mt-3 mb-1">
            <i class="ri-list-check mr-2 m-0 p-0" style="vertical-align:middle;"></i>Ver Backlog</a>
          @if ($reasignar>0)
          <a href="{{ route('periodos.vencidos') }}" class="col-auto btn btn-plain danger btn-sm ml-4 mt-3 mb-0">
            <i class="ri-error-warning-line mr-1" style="vertical-align:middle;"></i>Hay incidentes a reprogramar</a>
          @endif
        </div>
    </div>
    <hr class="mb-3 mt-0">

    {{-- <table class="table">
        <thead>
          <tr>
            <th class="d-none d-md-table-cell" style="width:5rem;">ID</th>
            <th class="th-auto">Descripcion</th>
            <th class="d-none d-sm-table-cell text-center" style="width:8rem;">Desde</th>
            <th class="d-none d-sm-table-cell text-center" style="width:8rem;">Hasta</th>
            <th style="text-align:right;width:7rem;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($periodos as $periodo)
          <tr id="{{$periodo->codigo}}">
            <td class="d-none d-md-table-cell">{{$periodo->codigo}}</td>
            <td>{{$periodo->descripcion}}</td>
            <td class="d-none d-sm-table-cell text-center">{{Carbon::parse($periodo->desde)->format('d-m-Y')}}</td>
            <td class="d-none d-sm-table-cell text-center">{{Carbon::parse($periodo->hasta)->format('d-m-Y')}}</td>
            <td class="text-right no-pointer" style="word-spacing:.5rem;"> 
                <a href="{{ route('periodos.show', $periodo->codigo) }}" class="ri-lg ri-eye-line"></a>

                <a href="{{ route('periodos.edit', $periodo->codigo) }}" class="ri-lg ri-edit-line"></a>

                <a href="#" class="ri-lg ri-delete-bin-7-line" 
                    onclick="window.confirm('Esta seguro que desea eliminar el grupo?')?
                    (document.getElementById('form-delete').setAttribute('action','{{ route('grupos.destroy', $periodo->codigo) }}') &
                    document.getElementById('form-delete').submit()):''"
                ></a>
            </td>
            
          </tr>
          @endforeach
        </tbody>
    </table> --}}

    @php
      function getMonth($m) {
        if ($m==1) return 'ENE';
        if ($m==2) return 'FEB';
        if ($m==3) return 'MAR';
        if ($m==4) return 'ABR';
        if ($m==5) return 'MAY';
        if ($m==6) return 'JUN';
        if ($m==7) return 'JUL';
        if ($m==8) return 'AGO';
        if ($m==9) return 'SEP';
        if ($m==10) return 'OCT';
        if ($m==11) return 'NOV';
        if ($m==12) return 'DIC';
        return '';
      }
    @endphp

    <div class="row mt-4 mr-0" style="position:relative;padding-top:3px">
      @foreach ($calendar as $key => $val)
      <div class="col-12 d-flex pr-0">
        <div class="d-flex sprint-header" style="height:{{$val*3}}px;">
          {{getMonth($key)}}
        </div>

        <div class="sprint-background" style="height: {{$val*3}}px;">
          
        </div>
      </div>
      @endforeach

      @foreach ($timeline as $sprint)

      <a href="{{route('periodos.show', $sprint['codigo'])}}">
        <div class="sprint-item @if($sprint['activo']) active @endif" 
          style="top:{{$sprint['inicio'] * 3}}px;height:{{($sprint['fin']-$sprint['inicio']+1)*3}}px;
          @if($sprint['dias']<5) font-size:.65rem; @endif">
            @if($sprint['dias']>3) 
            <div class="row">
              <div class="col-6 text-white">{{$sprint['nombre']}}</div>
              <div class="col-6 text-white text-right pr-4" style="font-size:.75rem;padding-top:4px;opacity:.8">{{$sprint['fecha']}}</div>
            </div>
            @endif            
        </div>
      </a>
      @endforeach

    </div>

    



    {{-- @if ($periodos->hasMorePages())
    {{ $periodos->appends(request()->query())->links(true) }}
    @endif --}}

    {{-- <form id="form-delete" method="post" action="">
      @csrf 
      @method('delete')
    </form> --}}

</div>


@endsection