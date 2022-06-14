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
    
    <!-- Sombra para el sidebar mobile -->
    <div class="overlay"></div>

    <!-- Fondo para el sidebar desktop -->
    <div id="background" class="d-none d-sm-inline background"></div>
    
    <!-- Menu de navegacion -->
    <nav class="navbar">
        <div class="d-flex">
            <span class="d-inline d-sm-none dismiss fa fa-bars text-white ml-3 mr-1" style="margin-top:1.2rem;"></span>
            <a href="{{HOME}}"><li class="navbar-brand text-white">
              <span class="d-none d-md-inline" style="font-size: 1.25rem;">Gestion de </span>Incidentes</li></a>
        </div>
        @if ( Route::has('login') )
        <div class="row mr-2">
            @if ( Auth::user()->Usuario )
            <span class="nav-item">
              {{ Auth::user()->nombre }}
            </span>
            <span class="nav-btn">
              <a href="{{ route('logout') }}" class="text-white nav-link">
              <i class="fa fa-sign-out-alt" aria-hidden="true"></i></a>
            </span>
            @else
            <span class="nav-btn">
              <a href="{{ route('login') }}" class="text-white nav-link">
              <i class="fa fa-sign-in-alt mr-2" aria-hidden="true"></i>@lang('login.login')</a>
            </span>
            @endif
        </div>
        @endif
    </nav>
    

    <main>
      <div class="page-container">

        <!-- Sidebar desktop -->
        <nav class="d-none d-sm-inline sidebar" id="navbar">
          <div class="list-group">

            <div class="sidebar-header">
              <span class="ml-0">Menu</span>
            </div>

            <ul class="list-unstyled mt-2" id="submenu">

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='incidentes') active @endif">
                  <a href="{{ route('incidentes') }}">Lista de incidentes</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='incidente.crear') active @endif">
                  <a href="{{ route('incidente.crear') }}">Nuevo incidente</a>
              </li>

              @if (Auth::user()->cliente==5)
              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='cargamasiva') active @endif">
                  <a href="{{ route('cargamasiva') }}">Carga masiva</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='informes') active @endif">
                  <a href="{{ route('informes') }}">Informes</a>
              </li>
              @endif
            
            </ul>

            @can ('isadmin')
            <div class="sidebar-header">
              <span class="ml-0">Administrador</span>
            </div>

            <ul class="list-unstyled mt-2" id="submenu">

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='usuarios') active @endif">
                  <a href="{{ route('usuarios') }}">Usuarios</a>
              </li>
              
              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='clientes') active @endif">
                  <a href="{{ route('clientes') }}">Clientes</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='areas') active @endif">
                  <a href="{{ route('areas') }}">Areas</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='modulos') active @endif">
                  <a href="{{ route('modulos') }}">Módulos</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='tipoincidente') active @endif">
                  <a href="{{ route('tipoincidente') }}">Tipos de incidente</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='tiposervicio') active @endif">
                  <a href="{{ route('tiposervicio') }}">Tipos de servicio</a>
              </li>

            </ul>
            @endcan

          </div>
        </nav>

        <!-- Sidebar mobile -->
        <nav class="d-inline d-sm-none mobile-sidebar" id="navbar">
          <div class="list-group">

            <div class="sidebar-header">
              <span class="ml-0">Menu</span>
            </div>

            <ul class="list-unstyled mt-2" id="submenu">

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='incidentes') active @endif">
                  <a href="{{ route('incidentes') }}">Lista de incidentes</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='incidente.crear') active @endif">
                  <a href="{{ route('incidente.crear') }}">Nuevo incidente</a>
              </li>

              @if (Auth::user()->cliente==5)
              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='cargamasiva') active @endif">
                  <a href="{{ route('cargamasiva') }}">Carga masiva</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='informes') active @endif">
                  <a href="{{ route('informes') }}">Informes</a>
              </li>
              @endif
            
            </ul>

            @can ('isadmin')
            <div class="sidebar-header">
              <span class="ml-0">Administrador</span>
            </div>

            <ul class="list-unstyled mt-2" id="submenu">

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='usuarios') active @endif">
                  <a href="{{ route('usuarios') }}">Usuarios</a>
              </li>
              
              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='clientes') active @endif">
                  <a href="{{ route('clientes') }}">Clientes</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='areas') active @endif">
                  <a href="{{ route('areas') }}">Areas</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='modulos') active @endif">
                  <a href="{{ route('modulos') }}">Módulos</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='tipoincidente') active @endif">
                  <a href="{{ route('tipoincidente') }}">Tipos de incidente</a>
              </li>

              <li class="sidebaritem @if(Route::getCurrentRoute()->name=='tiposervicio') active @endif">
                  <a href="{{ route('tiposervicio') }}">Tipos de servicio</a>
              </li>

            </ul>
            @endcan

          </div>
        </nav>

        <!-- Contenido principal -->
        <div class="main-content" id="main-content">
          @yield('content')
        </div>

      </div>
    </main>


    <!-- Notificacion mensajes ok y error -->
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
    
    <!-- Notificaciones para varios errores (lista) -->
    @if ( $errors->any() )
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
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach       
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

  <script src="{{ asset('assets/js/highlight.min.js') }}"></script>
  <script>hljs.initHighlightingOnLoad();</script>


  <script type="text/javascript">

    $(document).ready(function () {

        $('.toast').toast('show')

        $('.dismiss').on('click', function () {
            if ($('.mobile-sidebar').hasClass("active"))
            {
                $('.mobile-sidebar').removeClass('active');
                $('.overlay').removeClass('active');
            }
            else
            {
                $('.mobile-sidebar').addClass('active');
                $('.overlay').addClass('active');
            }
        });

        $('.overlay').on('click', function () {
            $('.mobile-sidebar').removeClass('active');
            $('.overlay').removeClass('active');
        });

    });

  </script>
</html>