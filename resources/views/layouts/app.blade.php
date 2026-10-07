<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('\img\Livelogo-wobg.png') }}">


    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <!-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> -->

    <!-- Scripts -->
    <!-- Vite scripts removed to use offline assets directly -->



    <!-- Font Awesome CSS -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <!-- DataTables CSS -->

    <!-- {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css"> --}} -->
    <!-- Offline Bootstrap 5.3.8 CSS -->
    <link rel="stylesheet" type="text/css" href="/bootstrap-5.3.8/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap4.css"> -->
    @auth
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/select.dataTables.min.css">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="/css/jquery-ui.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">
    @endauth

    <!-- Calander -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" /> -->


    <link href="/css/loader.css" rel="stylesheet">
    <link href="/css/common.css" rel="stylesheet">


    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <script src="/js/jquery.min.js"></script>

    <!-- Offline Bootstrap 5.3.8 JS Bundle (includes Popper) -->
    <script src="/bootstrap-5.3.8/js/bootstrap.bundle.min.js"></script>

    @auth
    <!-- jQuery Validate -->
    <script src="/js/jquery.validate.js"></script>

    <!-- Bootstrap Typeahead -->
    <script src="/js/bootstrap3-typeahead.min.js"></script>

    <!-- DataTables -->
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/dataTables.buttons.min.js"></script>
    <script src="/js/buttons.html5.min.js"></script>
    <script src="/js/jszip.min.js"></script>
    <script src="/js/dataTables.responsive.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/select2.min.js"></script>
    @endauth



    <!-- Calander -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script> -->


</head>

<body>
    <div id="app">
        
        <nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm" style="padding: 0 0 0 0;">
            <div class="container-fluid">

                <a class="navbar-brand" href="{{ url('/home') }}">
                    {{-- {{ config('app.name', 'Laravel') }} --}}
                    <img src="/img/logo.png" style="width:170px;height:45px" />

                </a>


                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->

                    <ul class="navbar-nav me-auto">
                        @include('layouts.navbarmenus')
                    </ul>


                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">


                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else

                        
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='fa fa-question-circle' style='font-size:16px'></i>
                        </button>

                        <div class="collapse" id="collapseExample">
                            <div class="card card-body" style="padding: .5rem;">
                                Techinical issues, please contact - 9994404022
                            </div>
                        </div>

                            <li class="nav-item dropdown dropstart"  role="group">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <img src="/avatars/{{ (Auth::user()->avatar ?? null) ?: 'default.png' }}" style="width: 40px; border-radius: 30%">
                                    {{-- <img src="/avatars/{{ Auth::user()->avatar }}" style="width: 50px; border-radius: 30%"> --}}
                                    {{-- {{ Auth::user()->name }} --}}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="float:right;">
                                    <a href="{{ route('user.profile') }}" class="dropdown-item">Profile</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

       

        <main class="py-2">
            @include('alert.flashMessage')
            @yield('content')
        </main>
    </div>
</body>

</html>
