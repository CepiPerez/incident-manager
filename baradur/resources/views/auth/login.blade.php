@extends('layouts.header')

@section('content')
<div class="container" style="max-width:400px;">

<div class="card text-center">

    <!-- <div class="card-header">
        Ingreso al sistema
    </div> -->

    <div class="container p-3">

        
        <img src="{{ asset('assets/logonewrol.png') }}" alt="" height="72px;">
        <!-- {if $mensaje}
        <div class="alert alert-{$mensaje.type} alert-dismissible" role="alert">{$mensaje.text}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {/if} -->
        
        <div class="card-body">
            <form action="{{ route('confirm_login') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" autofocus style="text-align:center;">
                </div>
                <div class="form-group">
                    <label for="password">Clave</label>
                    <input type="password" id="password" name="password" 
                    class="form-control {if $mensaje}is-invalid{/if}" 
                    placeholder="Ingrese la clave" style="text-align:center;">
                    <div id="passwordFeedback" class="invalid-feedback">
                        {$mensaje.text}
                    </div>
                </div>
                <br>
                <button type="submit" class="btn btn-info">Ingresar</button>
            </form>
        </div>
    </div>

</div>

</div>

</div>
@endsection
