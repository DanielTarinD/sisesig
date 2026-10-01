<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Sistema de Pacientes')
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* =========================================================
           GENERAL
           ========================================================= */

        body {
            background: #f5f7fb;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .card {
            border: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        }

        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .metric {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .min-width-0 {
            min-width: 0;
        }


        /* =========================================================
           CAPTURA
           ========================================================= */

        .capture-section {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .capture-section-title {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .capture-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: #eef4ff;
            color: #0d6efd;
            font-size: 1.2rem;
        }

        .capture-label {
            font-weight: 600;
            margin-bottom: .45rem;
        }

        .capture-input {
            min-height: 52px;
            border-radius: 12px;
            font-size: 1rem;
            padding: .7rem .9rem;
        }

        .capture-input:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12);
        }

        .capture-suggestions {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            z-index: 1050;
            max-height: 260px;
            overflow: auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
        }

        .capture-suggestions .list-group-item {
            min-height: 52px;
            display: flex;
            align-items: center;
        }

        .capture-toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .9rem 1rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .capture-switch .form-check-input {
            width: 3.2rem;
            height: 1.8rem;
            margin: 0;
            cursor: pointer;
        }

        .capture-check-card {
            min-height: 54px;
            width: 100%;
            padding: .75rem .85rem;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: .7rem;
            cursor: pointer;
            transition: .15s ease;
            background: #fff;
        }

        .capture-check-card .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin: 0;
            flex: none;
        }

        .capture-check-card i {
            margin-left: auto;
            color: #198754;
            opacity: 0;
        }

        .capture-check-card:has(input:checked) {
            border-color: #86b7fe;
            background: #f0f6ff;
        }

        .capture-check-card:has(input:checked) i {
            opacity: 1;
        }

        .capture-actions {
            display: flex;
            gap: .75rem;
            justify-content: flex-end;
        }

        .capture-actions .btn {
            min-height: 50px;
            padding: .7rem 1rem;
            border-radius: 12px;
        }

        .followup-input {
            min-height: 56px;
            font-size: 1.1rem;
            text-align: center;
            font-weight: 600;
        }

        .followup-label {
            font-size: .9rem;
            font-weight: 600;
        }


        /* =========================================================
           NAVEGACIÓN
           ========================================================= */

        .main-navbar {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
        }

        .navbar-brand {
            white-space: nowrap;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, .35);
            padding: .45rem .65rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .2rem rgba(255, 255, 255, .15);
        }

        .navbar-nav .nav-link {
            border-radius: 8px;
            transition: background-color .15s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            background: rgba(255, 255, 255, .08);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: rgba(255, 255, 255, .85);
        }

        .navbar-user-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .navbar-user-info {
            line-height: 1.15;
        }

        .navbar-user-name {
            font-size: .875rem;
            font-weight: 600;
        }

        .navbar-user-role {
            font-size: .72rem;
            opacity: .7;
        }


        /* =========================================================
           ALERTAS
           ========================================================= */

        .alert {
            border-radius: 12px;
        }


        /* =========================================================
           MOBILE
           ========================================================= */

        @media (max-width: 991.98px) {

            .navbar-collapse {
                margin-top: .75rem;
                padding-top: .75rem;
                border-top: 1px solid rgba(255, 255, 255, .15);
            }

            .navbar-nav {
                gap: .15rem;
            }

            .navbar-nav .nav-link {
                padding: .7rem .8rem;
            }

            .navbar-user {
                margin-top: .75rem;
                padding: .75rem 0;
                border-top: 1px solid rgba(255, 255, 255, .15);
                border-bottom: 1px solid rgba(255, 255, 255, .15);
            }

            .navbar-logout {
                margin-top: .75rem;
            }

            .navbar-logout .btn {
                width: 100%;
                min-height: 44px;
            }
        }


        @media (max-width: 767.98px) {

            body {
                background: #f5f7fb;
            }

            main.container {
                padding-left: .75rem;
                padding-right: .75rem;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .page-heading {
                margin-bottom: 1rem !important;
            }

            .page-heading h2 {
                font-size: 1.35rem;
            }

            .capture-section {
                padding: .9rem;
                border-radius: 14px;
            }

            .capture-section-title {
                margin-bottom: .85rem;
            }

            .capture-section-title h5 {
                font-size: 1rem;
            }

            .capture-actions {
                position: sticky;
                bottom: .5rem;
                z-index: 1000;
                background: rgba(245, 247, 251, .95);
                padding: .65rem;
                margin: 0 -.25rem;
                border: 1px solid #e9ecef;
                border-radius: 14px;
                box-shadow: 0 4px 18px rgba(0, 0, 0, .12);
            }

            .capture-actions .btn {
                flex: 1;
            }

            .followup-actions {
                position: sticky;
                bottom: .5rem;
                z-index: 1000;
                background: rgba(255, 255, 255, .95);
                padding: .65rem;
                margin: 0 -.25rem;
                border-radius: 14px;
                box-shadow: 0 4px 18px rgba(0, 0, 0, .12);
            }

            .mobile-hide-text {
                display: none;
            }

        }


        /* =========================================================
           IMPRESIÓN
           ========================================================= */

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .card {
                box-shadow: none;
            }

        }
    </style>

    @stack('styles')

</head>


<body>


    {{-- =========================================================
     NAVEGACIÓN
     ========================================================= --}}

    <nav class="navbar navbar-expand-lg bg-dark navbar-dark main-navbar no-print">

        <div class="container">

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-heart-pulse me-1"></i>
                Sistema de Pacientes
            </a>


            @auth

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Abrir menú">

                    <span class="navbar-toggler-icon"></span>

                </button>


                <div class="collapse navbar-collapse" id="mainNavbar">

                    {{-- MENÚ --}}
                    <ul class="navbar-nav me-auto">

                        <li class="nav-item">

                            <a class="nav-link" href="{{ route('patients.index') }}">
                                <i class="bi bi-people me-1"></i>
                                Pacientes
                            </a>

                        </li>


                        <li class="nav-item">

                            <a class="nav-link" href="{{ route('reports.global') }}">
                                <i class="bi bi-bar-chart-line me-1"></i>
                                Reporte global
                            </a>

                        </li>


                        @if (auth()->user()->isCapturista())

                            <li class="nav-item">

                                <a class="nav-link" href="{{ route('patients.create') }}">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Nuevo paciente
                                </a>

                            </li>


                            <li class="nav-item">

                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <i class="bi bi-person-gear me-1"></i>
                                    Usuarios
                                </a>

                            </li>

                            @if (auth()->user()->isCapturista())

                                <li class="nav-item dropdown">

                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-collection"></i>
                                        Catálogos
                                    </a>

                                    <ul class="dropdown-menu">

                                        <li>
                                            <a class="dropdown-item" href="{{ route('comorbidities.index') }}">
                                                <i class="bi bi-heart-pulse me-2"></i>
                                                Comorbilidades
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ route('colonias.index') }}">
                                                <i class="bi bi-geo-alt me-2"></i>
                                                Colonias
                                            </a>
                                        </li>

                                    </ul>

                                </li>

                            @endif

                        @endif

                    </ul>


                    {{-- USUARIO --}}
                    <div class="navbar-user">

                        <div class="navbar-user-icon">

                            <i class="bi bi-person"></i>

                        </div>

                        <div class="navbar-user-info">

                            <div class="navbar-user-name">

                                {{ auth()->user()->name }}

                            </div>

                            <div class="navbar-user-role">

                                {{ auth()->user()->isCapturista() ? 'Capturista' : 'Visor' }}

                            </div>

                        </div>

                    </div>


                    {{-- SALIR --}}
                    <div class="navbar-logout ms-lg-3">

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button type="submit" class="btn btn-outline-light btn-sm">

                                <i class="bi bi-box-arrow-right me-1"></i>

                                Salir

                            </button>

                        </form>

                    </div>

                </div>

                @endif

            </div>

        </nav>


        {{-- =========================================================
     CONTENIDO
     ========================================================= --}}

        <main class="container py-4">


            @if (session('success'))

                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">

                    <i class="bi bi-check-circle me-1"></i>

                    {{ session('success') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>

                </div>

            @endif


            @if ($errors->any())

                <div class="alert alert-danger no-print" role="alert">

                    <strong>
                        Revise los siguientes campos:
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            @endif


            @yield('content')

        </main>


        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        @stack('scripts')


        {{-- Cerrar menú automáticamente después de seleccionar una opción --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const navbar = document.getElementById('mainNavbar');

                if (!navbar) {
                    return;
                }

                navbar.querySelectorAll('.nav-link').forEach(function(link) {

                    link.addEventListener('click', function() {

                        if (window.innerWidth < 992) {

                            const collapse =
                                bootstrap.Collapse.getInstance(navbar);

                            if (collapse) {
                                collapse.hide();
                            }

                        }

                    });

                });

            });
        </script>


    </body>

    </html>
