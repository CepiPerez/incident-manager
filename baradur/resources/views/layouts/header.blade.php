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
    
    
    <!-- Menu de navegacion -->
    <nav class="navbar">
        <div class="d-flex">
            <span class="d-inline d-sm-none dismiss ri-menu-fill text-white ml-3 mr-1" style="margin-top:1rem;"></span>
            <a href="{{HOME}}"><li class="navbar-brand text-white">
              <span class="d-none d-md-inline" style="font-size: 1.25rem;">Gestion de </span>Incidentes</li></a>
        </div>

        <div class="d-flex" style="margin:0; padding:0; top:-1rem;">

          <span class="nav-btn" style="padding:.45rem .75rem;margin-right:1rem;" id="theme-toggle">
            <i class="ri-lg ri-sun-line" style="line-height:21px;font-size:15px;vertical-align:middle;" id="theme-toggle-light-icon" hidden></i>
            <i class="ri-lg ri-moon-line" style="line-height:21px;font-size:15px;vertical-align:middle;" id="theme-toggle-dark-icon" hidden></i>
          </span>
  
        </div>
    </nav>
    

    <main>
      <div class="page-container loading" id="maincontent">

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
  </body>

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

      setTimeout(setLoaded, 600);

    });

  </script>
</html>