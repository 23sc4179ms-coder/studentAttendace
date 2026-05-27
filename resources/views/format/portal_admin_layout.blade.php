<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EGG ALL Project')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/eggall.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <!-- <script src="{{ asset('js/jQuery.js') }}"></script> -->
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body>
    <div class="app-shell">
      <aside class="sidebar" aria-label="Sidebar">
        <div class="sidebar__inner">
          <a href="/home" class="site-brand" aria-label="EGG ALL Home">
            <span class="site-brand__mark" aria-hidden="true"></span>
            <span class="site-brand__text">EGG ALL</span>
          </a>

          <nav class="nav" aria-label="Primary">
            <ul class="nav__list">
              <li><a class="nav__link" href="{{ route('student.index') }}">Home</a></li>
              <li><a class="nav__link {{ request()->is('degree*') ? 'is-active' : '' }}" href="/degree">Degree Page</a></li>
              <li><a class="nav__link" href="{{ route('logout') }}">Log Out</a></li>
            </ul>
          </nav>
        </div>
      </aside>

      <div class="app-main">
        <main class="content" role="main">
          @yield('content')
        </main>

        @section('footer')
        <footer class="footer">
          <p>Copyright &copy; {{ date('Y') }} lhemster</p>
        </footer>
        @show
      </div>
    </div>

    @stack('modals')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>