@extends('layouts.main')

@section('content')
<link href="{{ asset('assets/css/pagination.css') }}" rel="stylesheet">

<div class="container mb-3">

    <div class="row mr-0">
        <h3 class="col pt-2">Mis tareas</h3>

        <div class="col-lg botonera pr-0 pl-3">

            @if ($sin_asignar>0)
                <a href="{{config('app.url')}}/incidentes?orden=&cliente=todos&grupo=todos&usuario=todos&tipo_incidente=todos&modulo=todos&status=sin_asignar&prioridad=todos"
                    {{--  onclick="sinAsignar()" --}} class="col-auto btn btn-plain danger btn-sm ml-2 mt-3 mb-0">
                <i class="ri-error-warning-line mr-1" style="vertical-align:middle;"></i>Hay incidentes sin asignar</a>
            @endif
                
        </div>
    </div>
    <hr class="mb-4 mt-0">

    @if ($dentro->count()>0)
        <br>
        <h4>Incidentes del Sprint actual</h4>
        <hr class="mb-0 mt-2">

        <table class="table ticketera">
            <thead>
            <tr>
                <th style="width:3.5rem;">
                    P
                </th>
                <th style="width:6rem;padding-left:0;">
                    Inc
                </th>
                <th class="th-auto">
                    Descripcion
                </th>
                <th class="d-none d-xl-table-cell" style="width:170px;">
                    Creado
                </th>
                {{-- <th class="d-none d-lg-table-cell" style="width:150px;">
                    Asignado
                </th> --}}
                <th class="d-none d-md-table-cell text-center" style="width:120px;">
                    Estado
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($dentro as $value)
                <tr>
                    <td class="pl-1">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                            <img src="{{asset('assets/icons/'.$value->pid.'.svg')}}" alt="" class="priority">
                        </a>
                    </td>
                    <td class="pl-0 text-secondary">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        {{ str_pad($value->id, 7, '0', STR_PAD_LEFT) }}
                        </a>                        
                    </td>
                    <td class="td-truncated">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        @if (Auth::user()->tipo==1)
                        <span class="mr-2" style="font-weight:600;">{{ $value->cliente_desc }}</span>
                        {{ $value->titulo }}
                        @else
                        <span class="mr-2 text-secondary" style="font-weight:500;">{{ $value->titulo }}</span>
                        @endif
                        </a>
                    </td>
                    <td class="d-none d-xl-table-cell">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        <span style="font-weight:500;">{{ $value->fecha_ingreso->rawFormat('d-m-Y') }}</span>
                        <span class="text-secondary" style="font-size:.75rem;">{{ $value->fecha_ingreso->rawFormat(' H:i') }}</span>
                        </a>
                    </td>
                    {{-- <td class="d-none d-lg-table-cell td-truncated">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        @if ($value->status!=0 && $value->asignado)
                            <img src="{{ $value->inc_asignado->avatar }}" alt="">
                            <span class="text-secondary">{{ $value->inc_asignado->nombre }}</span>
                        @else
                            <img src="{{ Storage::url('/profile/unassigned.png') }}" alt="">
                            <span class="text-dimm">Sin asignar</span>
                        @endif
                        </a>
                    </td> --}}
                    <td class="d-none d-md-table-cell text-center">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        <i class="badge
                        @if ($value->status==0) badge-orange
                        @elseif ($value->status==5) badge-teal
                        @elseif ($value->status==6) badge-red
                        @elseif ($value->status==10) badge-green
                        @elseif ($value->status==20) badge-gray
                        @elseif ($value->status==50) badge-lightgray
                        @else badge-blue
                        @endif
                        ">{{ $value->status_desc }}</i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif

    @if ($fuera->count()>0)
        <br>
        <h4>Incidentes fuera del sprint</h4>
        <hr class="mb-0 mt-2">

        <table class="table ticketera">
            <thead>
            <tr>
                <th style="width:3.5rem;">
                    P
                </th>
                <th style="width:6rem;padding-left:0;">
                    Inc
                </th>
                <th class="th-auto">
                    Descripcion
                </th>
                <th class="d-none d-xl-table-cell" style="width:170px;">
                    Creado
                </th>
                {{-- <th class="d-none d-lg-table-cell" style="width:150px;">
                    Asignado
                </th> --}}
                <th class="d-none d-md-table-cell text-center" style="width:120px;">
                    Estado
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($fuera as $value)
                <tr>
                    <td class="pl-1">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                            <img src="{{asset('assets/icons/'.$value->pid.'.svg')}}" alt="" class="priority">
                        </a>
                    </td>
                    <td class="pl-0 text-secondary">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        {{ str_pad($value->id, 7, '0', STR_PAD_LEFT) }}
                        </a>                        
                    </td>
                    <td class="td-truncated">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        @if (Auth::user()->tipo==1)
                        <span class="mr-2" style="font-weight:600;">{{ $value->cliente_desc }}</span>
                        {{ $value->titulo }}
                        @else
                        <span class="mr-2 text-secondary" style="font-weight:500;">{{ $value->titulo }}</span>
                        @endif
                        </a>
                    </td>
                    <td class="d-none d-xl-table-cell">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        <span style="font-weight:500;">{{ $value->fecha_ingreso->rawFormat('d-m-Y') }}</span>
                        <span class="text-secondary" style="font-size:.75rem;">{{ $value->fecha_ingreso->rawFormat(' H:i') }}</span>
                        </a>
                    </td>
                    {{-- <td class="d-none d-lg-table-cell td-truncated">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        @if ($value->status!=0 && $value->asignado)
                            <img src="{{ $value->inc_asignado->avatar }}" alt="">
                            <span class="text-secondary">{{ $value->inc_asignado->nombre }}</span>
                        @else
                            <img src="{{ Storage::url('/profile/unassigned.png') }}" alt="">
                            <span class="text-dimm">Sin asignar</span>
                        @endif
                        </a>
                    </td> --}}
                    <td class="d-none d-md-table-cell text-center">
                        <a href="{{ route('incidentes.show', (int)$value->id) }}">
                        <i class="badge
                        @if ($value->status==0) badge-orange
                        @elseif ($value->status==5) badge-teal
                        @elseif ($value->status==6) badge-red
                        @elseif ($value->status==10) badge-green
                        @elseif ($value->status==20) badge-gray
                        @elseif ($value->status==50) badge-lightgray
                        @else badge-blue
                        @endif
                        ">{{ $value->status_desc }}</i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @endif

    @if ($dentro->count()==0 && $fuera->count()==0)
        <p>No hay tareas pendientes</p>
    @endif

</div>

@endsection
