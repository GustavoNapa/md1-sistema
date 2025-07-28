<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MD1 Clients') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Fonts CSS -->
    <link href="{{ asset("css/custom-fonts.css") }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url("/") }}">
                    <img src="{{ asset("images/logo-simbolo-semfundo.png") }}" alt="MD1 Academy" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __("Toggle navigation") }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("clients.index") }}">Clientes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("products.index") }}">Produtos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("inscriptions.index") }}">Inscrições</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdownCadastros" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Cadastros
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownCadastros">
                                    <a class="dropdown-item" href="{{ route("achievement_types.index") }}">Tipos de Conquistas</a>
                                    <a class="dropdown-item" href="{{ route("clients.index") }}">Clientes</a>
                                    <a class="dropdown-item" href="{{ route("products.index") }}">Produtos</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route("import.index") }}">Importação</a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has("login"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route("login") }}">{{ __("app.login") }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->hasPermission("manage-permissions") || Auth::user()->hasPermission("manage-roles") || Auth::user()->hasPermission("manage-users"))
                                        <h6 class="dropdown-header">Gestão de Acessos</h6>
                                        
                                        @if(Auth::user()->hasPermission("manage-permissions"))
                                            <a class="dropdown-item" href="{{ route("permissions.index") }}">
                                                <i class="fas fa-shield-alt me-2"></i>Permissões
                                            </a>
                                        @endif
                                        
                                        @if(Auth::user()->hasPermission("manage-roles"))
                                            <a class="dropdown-item" href="{{ route("roles.index") }}">
                                                <i class="fas fa-user-tag me-2"></i>Cargos
                                            </a>
                                        @endif
                                        
                                        @if(Auth::user()->hasPermission("manage-users"))
                                            <a class="dropdown-item" href="{{ route("users.index") }}">
                                                <i class="fas fa-users me-2"></i>Usuários
                                            </a>
                                        @endif
                                        
                                        <hr class="dropdown-divider">
                                    @endif
                                    
                                    @if(Auth::user()->hasPermission("manage-integrations"))
                                        <a class="dropdown-item" href="{{ route("integrations.index") }}">
                                            <i class="fas fa-plug me-2"></i>Integrações
                                        </a>
                                        <hr class="dropdown-divider">
                                    @endif
                                    
                                    <a class="dropdown-item" href="{{ route("logout") }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById("logout-form").submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __("app.logout") }}
                                    </a>

                                    <form id="logout-form" action="{{ route("logout") }}" method="POST" class="d-none">
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
            <div class="container">
                @if (session("success"))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session("success") }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session("error"))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session("error") }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield("content")
            </div>
        </main>
    </div>

    <!-- jQuery deve vir antes de qualquer script que o utilize -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    @yield("scripts")
    <!-- Scripts -->
    @vite(["resources/sass/app.scss", "resources/js/app.js"])
</body>
</html>

