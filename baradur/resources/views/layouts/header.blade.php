<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <title>NewRol IT</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    
    <link href="{{ asset('assets/css/style3.css') }}" rel="stylesheet">

  </head>
  
  <body>

    <nav class="navbar">
        <div class="d-flex">
            <span class="dismiss fa fa-bars text-white ml-3 mr-1" style="margin-top:1.2rem;"></span>
            <a href="{{HOME}}"><li class="navbar-brand text-white">Gestion de Incidentes</li></a>
        </div>
    </nav>

    <div class="mt-4 d-flex justify-content-center">

      @yield('content')
    </div>
    
    @if ( session('message') || session('error') )
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" 
          data-delay="5000" style="position:absolute;top:1rem;right:1rem;opacity:1;">
      @if ( session('message') )
        <div class="toast-header bg-success" style="height:1rem;">
      @else
        <div class="toast-header bg-danger" style="height:1rem;">
      @endif
      </div>
      <div class="toast-body">
        <div class="row">
          <div class="col-auto pt-1 mr-3">
          @if ( session('message') )
            {{ session('message') }}
          @else
            {{ session('error') }}
          @endif          
          </div>
          <div class="col">
            <button type="button" class="ml-auto mb-1 close" data-dismiss="toast" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
    </div>
    </div>
    @endif


  </body>

  <script type="text/javascript">

    $(document).ready(function () {

        $('.toast').toast('show')

    });

  </script>
</html>
