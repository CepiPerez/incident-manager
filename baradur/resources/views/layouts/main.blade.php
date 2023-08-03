<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">

    <title>Incidentes</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style3.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ticketera.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/remixicon/remixicon.css') }}" rel="stylesheet">
    @stack('css')


    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    @stack('js')

    <script>
      var currentTheme;
      if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        currentTheme = 'dark';
      } else {
        document.documentElement.classList.remove('dark')
        currentTheme = 'light';
      }
    </script>

  </head>
  
  <body>
    
    <!-- Sombra para el sidebar mobile -->
    <div class="overlay"></div>

    <!-- Fondo para el sidebar desktop -->
    <div id="background" class="d-none d-sm-inline background loading"></div>
    
    <!-- Menu de navegacion -->
    <nav class="navbar">
        <div class="d-flex">
            <span class="d-inline d-sm-none dismiss ri-menu-fill text-white ml-3 mr-1" style="margin-top:1rem;"></span>
            <a href="{{route('incidentes.index')}}"><li class="navbar-brand text-white">
              <span class="d-none d-md-inline" style="font-size: 1.25rem;">Gestion de </span>Incidentes</li></a>
        </div>

        <div class="d-flex" style="margin:0; padding:0; top:-1rem;">

          <span class="nav-btn" style="padding:.45rem .75rem;margin-right:1rem;" id="theme-toggle">
            <i class="ri-lg ri-sun-line" style="line-height:21px;font-size:15px;vertical-align:middle;" id="theme-toggle-light-icon" hidden></i>
            <i class="ri-lg ri-moon-line" style="line-height:21px;font-size:15px;vertical-align:middle;" id="theme-toggle-dark-icon" hidden></i>
          </span>
  
          @if ( Route::has('login') )
          <div class="row mr-2" style="height:1rem;padding-top:.45rem;">
              @if ( Auth::check() )
            
              <div class="btn-group ml-2">
                  <a class="nav-btn mb-0 mt-0 pt-0 pb-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="row p-0">
                      <div class="col-auto nav-usertext ml-1">{{ Auth::user()->nombre }}</div>
                      <img src="{{ Auth::user()->avatar }}" alt="" class="nav-userpic">
                    </div>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right mr-2 mt-0">
                    <div class="text-center">
                      <img src="{{ Auth::user()->avatar }}" alt="" class="nav-userpic-big">
                      <button id="perfil" class="dropdown-item text-center" type="button">Perfil</button>
                      <button id="salir" class="dropdown-item text-center" type="button">Salir</button>
                    </div>
                  </div>
              </div>
  
              @else

              {{-- <span class="nav-btn">
                <a href="{{ route('login') }}" class="text-white nav-link">
                <i class="fa fa-sign-in-alt mr-2" aria-hidden="true"></i>@lang('login.login')</a>
              </span> --}}

              @endif
          </div>
          @endif

        </div>

    </nav>
    

    <main>
      <div class="page-container loading" id="maincontent">

        <!-- Sidebar desktop -->
        <nav class="d-none d-sm-inline sidebar mt-2" id="navbar">
          <div class="list-group">

            {{-- <div class="sidebar-header">
              <span class="ml-0">Menu</span>
            </div> --}}

            @if (auth()->user()->tipo==1)
            <ul class="list-unstyled mt-2 mb-0" id="submenu">
              <li class="sidebaritem @if(Route::currentRouteName()=='tareas') active @endif">
              <a href="{{ route('tareas') }}">
                  <i class="ri-task-line mr-2"></i>
                  <span>Mis tareas</span>
              </a>
              </li>
            </ul>
            @endif

            <ul class="list-unstyled mt-2 mb-0" id="submenu">


              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'incidentes') &&
                  !Str::contains(Route::currentRouteName(),'tipoincidentes') &&
                  !Str::contains(Route::currentRouteName(),'incidentes.create') &&
                  !Str::contains(Route::currentRouteName(),'incidentes.edit')) active @endif">
                  <a href="{{ route('incidentes.index') }}">
                    <i class="ri-chat-4-line"></i>
                    <span>Lista de incidentes</span>
                  </a>
              </li>

              @can('crear_inc')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'incidentes.create')) active @endif">
                  <a href="{{ route('incidentes.create') }}">
                    <i class="ri-chat-new-line"></i>
                    <span>Nuevo incidente</span>
                  </a>
              </li>
              @endcan

              {{-- @can('carga_masiva')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'cargamasiva')) active @endif">
                  <a href="{{ route('cargamasiva') }}">
                    <i class="ri-chat-upload-line"></i>
                    <span>Carga masiva</span>
                  </a>
              </li>
              @endcan --}}

              @can('tablero_control')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'dashboard')) active @endif">
                  <a href="{{ route('dashboard') }}">
                    <i class="ri-dashboard-3-line"></i>
                    <span>Tablero de control</span>
                  </a>
              </li>
              @endcan

              @can('informes')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'informes')) active @endif">
                  <a href="{{ route('informes') }}">
                    <i class="ri-bar-chart-box-line"></i>
                    <span>Informes</span>
                  </a>
              </li>
              @endcan

              @can('periodos')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'periodos')) active @endif">
                  <a href="{{ route('periodos.index') }}">
                    <i class="ri-calendar-check-line"></i>
                    <span>Calendario</span>
                  </a>
              </li>
              @endcan
            
            </ul>

            @can ('admin_panel')
          
            <ul class="list-unstyled mt-4" id="submenu">

              @can ('admin_usuarios')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'usuarios.')) active @endif">
                  <a href="{{ route('usuarios.index') }}">
                    <i class="ri-user-line"></i>
                    <span>Usuarios</span>
                  </a>
              </li>
              @endcan

              @can ('admin_grupos')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'grupos.')) active @endif">
                  <a href="{{ route('grupos.index') }}">
                    <i class="ri-group-line"></i>
                    <span>Grupos</span>
                  </a>
              </li>
              @endcan

              @can ('admin_clientes')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'clientes.')) active @endif">
                  <a href="{{ route('clientes.index') }}">
                    <i class="ri-user-2-line"></i>
                    <span>Clientes</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_roles')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'roles.')) active @endif">
                <a href="{{ route('roles.index') }}">
                  <i class="ri-shield-keyhole-line"></i>
                  <span>Roles</span>
                </a>
              </li>
              @endcan
              
              @can ('admin_areas')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'areas.')) active @endif">
                  <a href="{{ route('areas.index') }}">
                    <i class="ri-aspect-ratio-line"></i>
                    <span>Areas</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_modulos')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'modulos.')) active @endif">
                  <a href="{{ route('modulos.index') }}">
                    <i class="ri-picture-in-picture-line"></i>
                    <span>Módulos</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_tipoincidente')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'tipoincidentes.')) active @endif">
                  <a href="{{ route('tipoincidentes.index') }}">
                    <i class="ri-settings-6-line"></i>
                    <span>Tipos de incidente</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_tiposervicio')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'tiposervicios.')) active @endif">
                  <a href="{{ route('tiposervicios.index') }}">
                    <i class="ri-settings-line"></i>
                    <span>Tipos de servicio</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_tipoavance')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'tipoavances.')) active @endif">
                  <a href="{{ route('tipoavances.index') }}">
                    <i class="ri-send-plane-line"></i>
                    <span>Avances</span>
                  </a>
              </li>
              @endcan

              @if (Auth::user()->rol==1)
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'prioridades.') ||
                  Str::contains(Route::currentRouteName(),'reglas')) active @endif">
                  <a href="{{ route('prioridades.index') }}">
                    <i class="ri-alarm-warning-line"></i>
                    <span>Prioridades</span>
                  </a>
              </li>
              @endif

              @if (Auth::user()->rol==1)
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'asignaciones.') ||
                  Str::contains(Route::currentRouteName(),'asignaciones')) active @endif">
                  <a href="{{ route('asignaciones.index') }}">
                    <i class="ri-user-received-line"></i>
                    <span>Asignacion</span>
                  </a>
              </li>
              @endif

              @if (Auth::user()->rol==1)
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'herramientas.') ||
                  Str::contains(Route::currentRouteName(),'herramientas')) active @endif">
                  <a href="{{ route('herramientas') }}">
                    <i class="ri-tools-line"></i>
                    <span>Herramientas</span>
                  </a>
              </li>
              @endif


            </ul>
          
            @endcan

          </div>
        </nav>

        <div class="expand">
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" width="16" height="16"><path fill="none" d="M0 0h24v24H0z"/><path d="M10.828 12l4.95 4.95-1.414 1.414L8 12l6.364-6.364 1.414 1.414z"/></svg>
        </div>


        <!-- Sidebar mobile -->
        <nav class="d-inline d-sm-none mobile-sidebar" id="navbar">
          <div class="list-group">

            <div class="sidebar-header">
              <span class="ml-0">Menu</span>
            </div>

            @if (auth()->user()->tipo==1)
            <ul class="list-unstyled mt-2 mb-0" id="submenu">
              <li class="sidebaritem">
              <a href="{{ route('tareas') }}">
                  <i class="ri-task-line mr-2"></i>
                  <span>Mis tareas</span>
              </a>
              </li>
            </ul>
            @endif
              
            <ul class="list-unstyled mt-2 mb-0" id="submenu">

              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'incidentes') &&
                !Str::contains(Route::currentRouteName(),'incidentes.create') &&
                !Str::contains(Route::currentRouteName(),'incidentes.edit')) active @endif">
                  <a href="{{ route('incidentes.index') }}">
                    <i class="ri-chat-4-line"></i>
                    <span>Lista de incidentes</span>
                  </a>
              </li>

              @can('crear_inc')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'incidentes.create')) active @endif">
                  <a href="{{ route('incidentes.create') }}">
                    <i class="ri-chat-new-line"></i>
                    <span>Nuevo incidente</span>
                  </a>
              </li>
              @endcan
              

              {{-- @can('carga_masiva')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'cargamasiva')) active @endif">
                  <a href="{{ route('cargamasiva') }}">
                    <i class="ri-chat-upload-line"></i>
                    <span>Carga masiva</span>
                  </a>
              </li>
              @endcan --}}

              @can('tablero_control')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'dashboard')) active @endif">
                  <a href="{{ route('dashboard') }}">
                    <i class="ri-dashboard-3-line"></i>
                    <span>Tablero de control</span>
                  </a>
              </li>
              @endcan

              @can('informes')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'informes')) active @endif">
                  <a href="{{ route('informes') }}">
                    <i class="ri-bar-chart-box-line"></i>
                    <span>Informes</span>
                  </a>
              </li>
              @endcan

              @can('periodos')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'periodos')) active @endif">
                  <a href="{{ route('periodos.index') }}">
                    <i class="ri-calendar-check-line"></i>
                    <span>Calendario</span>
                  </a>
              </li>
              @endcan
            
            </ul>

            @can ('admin_panel')
          
            <ul class="list-unstyled mt-4" id="submenu">

              @can ('admin_usuarios')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'usuarios.')) active @endif">
                  <a href="{{ route('usuarios.index') }}">
                    <i class="ri-user-line"></i>
                    <span>Usuarios</span>
                  </a>
              </li>
              @endcan

              @can ('admin_grupos')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'grupos.')) active @endif">
                  <a href="{{ route('grupos.index') }}">
                    <i class="ri-group-line"></i>
                    <span>Grupos</span>
                  </a>
              </li>
              @endcan

              @can ('admin_clientes')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'clientes.')) active @endif">
                  <a href="{{ route('clientes.index') }}">
                    <i class="ri-user-2-line"></i>
                    <span>Clientes</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_roles')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'roles.')) active @endif">
                <a href="{{ route('roles.index') }}">
                  <i class="ri-shield-keyhole-line"></i>
                  <span>Roles</span>
                </a>
              </li>
              @endcan
              
              @can ('admin_areas')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'areas.')) active @endif">
                  <a href="{{ route('areas.index') }}">
                    <i class="ri-aspect-ratio-line"></i>
                    <span>Areas</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_modulos')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'modulos.')) active @endif">
                  <a href="{{ route('modulos.index') }}">
                    <i class="ri-picture-in-picture-line"></i>
                    <span>Módulos</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_tipoincidente')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'tipoincidentes.')) active @endif">
                  <a href="{{ route('tipoincidentes.index') }}">
                    <i class="ri-settings-6-line"></i>
                    <span>Tipos de incidente</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_tiposervicio')
              <li class="sidebaritem @if(Str::contains(Route::currentRouteName(),'tiposervicios.')) active @endif">
                  <a href="{{ route('tiposervicios.index') }}">
                    <i class="ri-settings-line"></i>
                    <span>Tipos de servicio</span>
                  </a>
              </li>
              @endcan
              
              @can ('admin_tipoavance')
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'tipoavances.')) active @endif">
                  <a href="{{ route('tipoavances.index') }}">
                    <i class="ri-send-plane-line"></i>
                    <span>Avances</span>
                  </a>
              </li>
              @endcan

              @if (Auth::user()->rol==1)
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'prioridades.') ||
                  Str::contains(Route::currentRouteName(),'reglas')) active @endif">
                  <a href="{{ route('prioridades.index') }}">
                    <i class="ri-alarm-warning-line"></i>
                    <span>Prioridades</span>
                  </a>
              </li>
              @endif

              @if (Auth::user()->rol==1)
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'asignaciones.') ||
                  Str::contains(Route::currentRouteName(),'asignaciones')) active @endif">
                  <a href="{{ route('asignaciones.index') }}">
                    <i class="ri-user-received-line"></i>
                    <span>Asignacion</span>
                  </a>
              </li>
              @endif

              @if (Auth::user()->rol==1)
              <li class="sidebaritem @if(Str::startsWith(Route::currentRouteName(),'herramientas.') ||
                  Str::contains(Route::currentRouteName(),'herramientas')) active @endif">
                  <a href="{{ route('herramientas') }}">
                    <i class="ri-tools-line"></i>
                    <span>Herramientas</span>
                  </a>
              </li>
              @endif


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
    @if ( $message || $error )
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false"
          {{-- data-delay="5000" --}} style="position:fixed;top:1rem;right:1rem;opacity:1;">
      @if ( $message )
        <div class="toast-header bg-success" style="height:1rem;">
      @else
        <div class="toast-header bg-danger" style="height:1rem;">
      @endif
      </div>
      <div class="toast-body">
        <div class="row">
          <div class="col-auto pt-1 mr-3">
          @if ( $message )
            {{ $message }}
          @else
            {{ $error }}
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
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false"
          {{-- data-delay="5000" --}} style="position:absolute;top:1rem;right:1rem;opacity:1;">
      {{-- @if ( session('message') )
        <div class="toast-header bg-success" style="height:1rem;">
      @else --}}
        <div class="toast-header bg-danger" style="height:1rem;">
      {{-- @endif --}}
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

    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    var themeToggleBtn = document.getElementById('theme-toggle');

    themeToggleBtn.addEventListener('click', function() {

      // if set via local storage previously
      if (localStorage.getItem('color-theme'))
      {
          if (localStorage.getItem('color-theme') === 'light') {
              localStorage.setItem('color-theme', 'dark');
              currentTheme = 'dark';
          } else {
              localStorage.setItem('color-theme', 'light');
              currentTheme = 'light';
          }

      // if NOT set via local storage previously
      } else {
          if (document.documentElement.classList.contains('dark')) {
              localStorage.setItem('color-theme', 'light');
              currentTheme = 'light';
          } else {
              localStorage.setItem('color-theme', 'dark');
              currentTheme = 'dark';
          }
      }

      changeIconTheme();
        
    });

    function changeIconTheme()
    {
      //console.log("THEME: "+currentTheme);

      if (currentTheme == 'dark') {
        document.documentElement.classList.add('dark');
        themeToggleDarkIcon.setAttribute('hidden', true);
        themeToggleLightIcon.removeAttribute('hidden');
      } else {
        document.documentElement.classList.remove('dark');
        themeToggleLightIcon.setAttribute('hidden', true);
        themeToggleDarkIcon.removeAttribute('hidden');
      }
    }

    function setLoaded() {
      $('#maincontent').addClass('loaded');
      $('#background').addClass('loaded');
    }

    $(document).ready(function () {

      changeIconTheme();

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

      $('#perfil').on('click', function () {
        javascript:window.location.href='{{ route("usuarios.perfil", Auth::user()->idT) }}';
      });

      $('#salir').on('click', function () {
        javascript:window.location.href='{{ route("logout") }}';
      });

      $('.expand').on('click', function () {
        if ($('.sidebar').hasClass("collapsed"))
        {
          $('.sidebar').removeClass('collapsed');
          $('.background').removeClass('collapsed');
          localStorage.setItem('sidebar', 'expanded');
        }
        else
        {
          $('.sidebar').addClass('collapsed');
          $('.background').addClass('collapsed');
          localStorage.setItem('sidebar', 'collapsed');
        }
      });


      if (localStorage.getItem('sidebar') === 'collapsed') {
        $('.sidebar').addClass('collapsed');
        $('.background').addClass('collapsed');
      }
      setTimeout(setLoaded, 600);


    });

  </script>
</html>