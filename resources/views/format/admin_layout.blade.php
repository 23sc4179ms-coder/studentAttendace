<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MLGA Project')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ request()->getSchemeAndHttpHost() . rtrim(preg_replace('#/index\\.php$#', '', request()->getBaseUrl()), '/') }}">
    <!-- <script src="{{ asset('js/jQuery.js') }}"></script> -->
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>
    

    <div class="page-shell">
      @section('header')
      <header class="site-header">
        <div class="site-header__inner">
          <a href="/home" class="site-brand" aria-label="MLGA Home">
            <span class="site-brand__mark" aria-hidden="true"></span>
            <span class="site-brand__text">MLGA Project</span>
          </a>

          <nav class="nav" aria-label="Primary">
            <ul class="nav__list">
            
            <li><a class="nav__link" href="{{ route('student.index') }}">Home</a></li>
              <li><a class="nav__link {{ request()->is('degree*') ? 'is-active' : '' }}" href="/degree">Degree Page</a></li>
              <li><a class="nav__link" href="{{ route('logout') }}">Log Out</a></li>
              
            </ul>
          </nav>
        </div>
      </header>
      @show

      <main class="content" role="main">
        @yield('content')
      </main>

      @section('footer')
      <footer class="footer">
        <p>Copyright &copy; {{ date('Y') }} lhemster</p>
      </footer>
      @show
    </div>

    @stack('modals')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>