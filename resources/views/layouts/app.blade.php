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
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Fonts CSS -->
    <link href="{{ asset("css/custom-fonts.css") }}" rel="stylesheet">
    <!-- Scripts -->
    @vite(["resources/sass/app.scss", "resources/js/app.js"])

    <style>
        html, body {
            height: 100%;
            overflow-y: auto;
        }
        #app {
            height: 100%;
        }
        .container-fluid.h-100 {
            height: calc(100vh - 56px); /* Altura total menos a navbar */
        }
        #whatsapp-sidebar {
            height: 100%;
            overflow-y: auto;
            padding-bottom: 15px; /* Espaço para o botão Carregar Mais */
        }
        #whatsapp-chat-panel {
            height: 100%;
        }
        #messages-container {
            flex-grow: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column-reverse; /* Para que as novas mensagens apareçam na parte inferior */
        }
        .message-bubble {
            max-width: 75%;
            padding: 8px 12px;
            border-radius: 10px;
            margin-bottom: 5px;
            word-wrap: break-word;
        }
        .message-outbound {
            background-color: #dcf8c6; /* Verde claro do WhatsApp */
            align-self: flex-end;
        }
        .message-inbound {
            background-color: #ffffff; /* Branco do WhatsApp */
            border: 1px solid #e0e0e0;
            align-self: flex-start;
        }
        .message-time {
            font-size: 0.75em;
            color: rgba(0, 0, 0, 0.5);
            margin-top: 3px;
        }
        .message-outbound .message-time {
            color: rgba(255, 255, 255, 0.7);
        }
        #message-input {
            resize: none;
        }
        @media (max-width: 767.98px) {
            #whatsapp-sidebar {
                position: absolute;
                width: 100%;
                z-index: 1000;
                background-color: #fff;
            }
            #whatsapp-chat-panel {
                width: 100%;
            }
            .d-md-block {
                display: none !important;
            }
            .d-md-none {
                display: block !important;
            }
        }
    </style>
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
                            @php
                                $navigationMap = include resource_path('views/layouts/navigation_map.php');
                            @endphp
                            
                            @foreach($navigationMap as $groupName => $items)
                                @php
                                    $hasAccessToGroup = false;
                                    foreach($items as $item) {
                                        if($item['route'] === 'leads.index' || auth()->user()->can($item['permission'])) {
                                            $hasAccessToGroup = true;
                                            break;
                                        }
                                    }
                                @endphp
                                
                                @if($hasAccessToGroup)
                                    <li class="nav-item dropdown">
                                        <a id="navbarDropdown{{ Str::slug($groupName) }}" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                            {{ $groupName }}
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown{{ Str::slug($groupName) }}">
                                            @foreach($items as $item)
                                                @if($item['route'] === 'leads.index')
                                                    <a class="dropdown-item" href="{{ route($item['route']) }}">
                                                        <i class="{{ $item['icon'] }} me-2"></i>{{ $item['name'] }}
                                                    </a>
                                                @else
                                                    @can($item['permission'])
                                                        <a class="dropdown-item" href="{{ route($item['route']) }}">
                                                            <i class="{{ $item['icon'] }} me-2"></i>{{ $item['name'] }}
                                                        </a>
                                                    @endcan
                                                @endif
                                            @endforeach
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has("login"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route("login") }}">{{ __("Entrar") }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @auth
                                        @php
                                            $adminItems = [
                                                [
                                                    'name' => 'Usuários',
                                                    'route' => 'users.index',
                                                    'permission' => 'users.index',
                                                    'icon' => 'fas fa-users',
                                                ],
                                                [
                                                    'name' => 'Cargos',
                                                    'route' => 'roles.index',
                                                    'permission' => 'roles.index',
                                                    'icon' => 'fas fa-user-tag',
                                                ],
                                                [
                                                    'name' => 'Permissões',
                                                    'route' => 'permissions.index',
                                                    'permission' => 'permissions.index',
                                                    'icon' => 'fas fa-shield-alt',
                                                ],
                                                [
                                                    'name' => 'Funcionalidades',
                                                    'route' => 'feature-flags.index',
                                                    'permission' => 'feature-flags.index',
                                                    'icon' => 'fas fa-toggle-on',
                                                ],
                                                [
                                                    'name' => 'Logs de Webhooks',
                                                    'route' => 'webhook-logs.index',
                                                    'permission' => 'webhook-logs.index',
                                                    'icon' => 'fas fa-exchange-alt',
                                                ],
                                                [
                                                    'name' => 'Integrações',
                                                    'route' => 'integrations.index',
                                                    'permission' => 'integrations.index',
                                                    'icon' => 'fas fa-plug',
                                                ],
                                            ];
                                        @endphp
                                        @foreach($adminItems as $item)
                                            @can($item['permission'])
                                                <a class="dropdown-item" href="{{ route($item['route']) }}">
                                                    <i class="{{ $item['icon'] }} me-2"></i>{{ $item['name'] }}
                                                </a>
                                            @endcan
                                        @endforeach
                                        <div class="dropdown-divider"></div>
                                    @endauth
                                    <a class="dropdown-item" href="{{ route("logout") }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
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
            <div class="container-fluid">
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

                {{-- ADDED: resumo de validação com links para cada campo --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="validation-summary">
                        <strong>Foram encontrados erros no formulário:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->messages() as $field => $messages)
                                <li>
                                    <a href="#" class="validation-link" data-field="{{ $field }}">
                                        <small><strong>{{ $field }}</strong></small>: {{ implode(' • ', $messages) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
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
    
    <!-- Toastr CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Toastr configuration -->
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    {{-- ADDED: script para navegar/realçar campos de erro ao clicar no resumo --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function findFieldElement(field) {
                if (!field) return null;
                // Try common selectors: id, name, name with [] (arrays), fallback replacing dots with underscores
                const candidates = [
                    `#${CSS.escape(field)}`,
                    `[name="${field}"]`,
                    `[name="${field}[]"]`,
                    `[name="${field}"]`,
                    `#${CSS.escape(field.replace(/\./g, '_'))}`,
                    `[name="${field.replace(/\./g, '_')}"]`
                ];
                for (const sel of candidates) {
                    try {
                        const el = document.querySelector(sel);
                        if (el) return el;
                    } catch (e) {
                        // ignore malformed selectors
                    }
                }
                // Last attempt: treat only the last segment (e.g. addresses.0.cep -> cep)
                const parts = field.split('.');
                if (parts.length > 1) {
                    const short = parts[parts.length - 1];
                    const el = document.querySelector(`#${CSS.escape(short)}`) || document.querySelector(`[name="${short}"]`);
                    if (el) return el;
                }
                return null;
            }

            function openContainingTab(el) {
                if (!el) return;
                const tabPane = el.closest('.tab-pane');
                if (tabPane && tabPane.id) {
                    // Find a tab button that targets this pane
                    const tabButton = document.querySelector(`[data-bs-target="#${tabPane.id}"], [data-bs-target="#${tabPane.id}"]`);
                    if (tabButton) {
                        try {
                            // Use Bootstrap's tab show via click for compatibility
                            tabButton.click();
                        } catch (e) {
                            // fallback: remove/show classes manually
                            tabButton.classList.add('active');
                        }
                    }
                }
            }

            document.querySelectorAll('.validation-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const field = this.dataset.field;
                    const el = findFieldElement(field);
                    if (!el) {
                        console.warn('Campo de validação não encontrado no DOM:', field);
                        return;
                    }

                    // If inside a tab, open it first
                    openContainingTab(el);

                    // Scroll to field and focus
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    try { el.focus({ preventScroll: true }); } catch (err) {}

                    // Apply temporary highlight
                    el.classList.add('border', 'border-danger', 'rounded');
                    setTimeout(() => {
                        el.classList.remove('border', 'border-danger', 'rounded');
                    }, 3500);
                });
            });
        });
    </script>

    @stack('scripts')
    @yield("scripts")
</body>
</html>
