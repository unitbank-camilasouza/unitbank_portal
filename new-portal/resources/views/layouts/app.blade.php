<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" charset="utf-8"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <title>{{ config('app.name', 'UNITFINANCE') }}</title>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'UNITFINANCE') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('consultant_login_form'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('consultant_login') }}">
                                        {{ __('Consultant Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('customer_login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('customer_login') }}">
                                        {{ __('Customer Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('form_consultant_register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('form_consultant_register') }}">
                                        {{ __('Register A Consultant') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('form_customer_register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('form_customer_register') }}">
                                        {{ __('Register A Customer') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if (Session::has('error'))
                <p class="text-danger" style="text-align: center;"> {{ Session::pull('error') }} </p>
            @endif

            @if (Session::has('message'))
                <p> {{ Session::pull('message') }} </p>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
