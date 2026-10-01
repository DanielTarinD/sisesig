@extends('layouts.app')

@section('title', 'Catálogo de Colonias')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
         ENCABEZADO
         ========================================================= --}}

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-geo-alt me-1"></i>
                Catálogo de Colonias
            </h1>

            <p class="text-muted mb-0">
                Administración de municipios y colonias.
            </p>
        </div>

        <a href="{{ route('colonias.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-circle me-1"></i>
            Nueva colonia

        </a>

    </div>


    {{-- =========================================================
         MENSAJE DE ÉXITO
         ========================================================= --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
         ERRORES
         ========================================================= --}}

    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            <i class="bi bi-exclamation-triangle me-1"></i>

            <strong>No fue posible realizar la operación.</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =========================================================
         BUSCADOR
         ========================================================= --}}

    <div class="card shadow-sm border-0 mb-3">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('colonias.index') }}">

                <div class="row g-2 align-items-end">

                    <div class="col-12 col-md-9">

                        <label for="search"
                               class="form-label fw-semibold">

                            Buscar colonia o municipio

                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ $search ?? '' }}"
                                   class="form-control"
                                   placeholder="Escriba municipio o colonia..."
                                   autocomplete="off">

                        </div>

                    </div>


                    <div class="col-12 col-md-3 d-flex gap-2">

                        <button type="submit"
                                class="btn btn-primary flex-fill">

                            <i class="bi bi-search me-1"></i>
                            Buscar

                        </button>


                        @if(($search ?? '') !== '')

                            <a href="{{ route('colonias.index') }}"
                               class="btn btn-outline-secondary"
                               title="Limpiar búsqueda">

                                <i class="bi bi-x-lg"></i>

                            </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         INFORMACIÓN DE RESULTADOS
         ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-2">

        <div class="small text-muted">

            @if($colonias->total() > 0)

                Mostrando

                <strong>
                    {{ $colonias->firstItem() }}
                </strong>

                a

                <strong>
                    {{ $colonias->lastItem() }}
                </strong>

                de

                <strong>
                    {{ $colonias->total() }}
                </strong>

                colonias

            @else

                No se encontraron colonias.

            @endif

        </div>

    </div>


    {{-- =========================================================
         LISTADO
         ========================================================= --}}

    <div class="card shadow-sm border-0">

        <div class="card-body p-0">


            {{-- =================================================
                 VISTA DESKTOP
                 ================================================= --}}

            <div class="table-responsive d-none d-md-block">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th style="width: 70px;">
                                #
                            </th>

                            <th>
                                Municipio
                            </th>

                            <th>
                                Colonia
                            </th>

                            <th class="text-center"
                                style="width: 120px;">

                                Estatus

                            </th>

                            <th class="text-end"
                                style="width: 130px;">

                                Acciones

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($colonias as $colonia)

                            <tr>

                                <td class="text-muted">

                                    {{ $colonias->firstItem() + $loop->index }}

                                </td>


                                <td>

                                    <span class="fw-semibold">

                                        {{ $colonia->municipio }}

                                    </span>

                                </td>


                                <td>

                                    {{ $colonia->name }}

                                </td>


                                <td class="text-center">

                                    @if($colonia->active)

                                        <span class="badge bg-success">
                                            Activa
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Inactiva
                                        </span>

                                    @endif

                                </td>


                                <td class="text-end">

                                    <div class="btn-group"
                                         role="group">


                                        {{-- EDITAR --}}

                                        <a href="{{ route('colonias.edit', $colonia) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        {{-- ACTIVAR / DESACTIVAR --}}

                                        <form action="{{ route('colonias.toggle', $colonia) }}"
                                              method="POST"
                                              class="d-inline">

                                            @csrf

                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-sm {{ $colonia->active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                    title="{{ $colonia->active ? 'Desactivar' : 'Activar' }}">

                                                @if($colonia->active)

                                                    <i class="bi bi-toggle-on"></i>

                                                @else

                                                    <i class="bi bi-toggle-off"></i>

                                                @endif

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center py-5 text-muted">

                                    <i class="bi bi-search fs-1 d-block mb-2"></i>

                                    @if(($search ?? '') !== '')

                                        No se encontraron colonias para:

                                        <strong>
                                            "{{ $search }}"
                                        </strong>

                                    @else

                                        No hay colonias registradas.

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                 VISTA MÓVIL
                 ================================================= --}}

            <div class="d-md-none p-3">

                @forelse($colonias as $colonia)

                    <div class="border rounded-3 p-3 mb-3">

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            <div class="flex-grow-1">

                                <div class="small text-muted mb-1">
                                    Municipio
                                </div>

                                <div class="fw-semibold">
                                    {{ $colonia->municipio }}
                                </div>


                                <div class="small text-muted mt-3 mb-1">
                                    Colonia
                                </div>

                                <div>
                                    {{ $colonia->name }}
                                </div>

                            </div>


                            <div>

                                @if($colonia->active)

                                    <span class="badge bg-success">
                                        Activa
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactiva
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="d-flex gap-2 mt-3">


                            {{-- EDITAR --}}

                            <a href="{{ route('colonias.edit', $colonia) }}"
                               class="btn btn-sm btn-outline-primary flex-fill">

                                <i class="bi bi-pencil me-1"></i>

                                Editar

                            </a>


                            {{-- ACTIVAR / DESACTIVAR --}}

                            <form action="{{ route('colonias.toggle', $colonia) }}"
                                  method="POST"
                                  class="flex-fill">

                                @csrf

                                @method('PATCH')

                                <button type="submit"
                                        class="btn btn-sm w-100 {{ $colonia->active ? 'btn-outline-danger' : 'btn-outline-success' }}">

                                    @if($colonia->active)

                                        <i class="bi bi-toggle-off me-1"></i>
                                        Desactivar

                                    @else

                                        <i class="bi bi-toggle-on me-1"></i>
                                        Activar

                                    @endif

                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-5 text-muted">

                        <i class="bi bi-search fs-1 d-block mb-2"></i>

                        @if(($search ?? '') !== '')

                            No se encontraron colonias para:

                            <strong>
                                "{{ $search }}"
                            </strong>

                        @else

                            No hay colonias registradas.

                        @endif

                    </div>

                @endforelse

            </div>


            {{-- =================================================
                 PAGINACIÓN PERSONALIZADA
                 ================================================= --}}

            @if($colonias->hasPages())

                <div class="border-top p-3">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">


                        {{-- Información de página --}}

                        <div class="small text-muted">

                            Página

                            <strong>
                                {{ $colonias->currentPage() }}
                            </strong>

                            de

                            <strong>
                                {{ $colonias->lastPage() }}
                            </strong>

                        </div>


                        {{-- Navegación --}}

                        <nav aria-label="Navegación de colonias">

                            <ul class="pagination colonias-pagination mb-0">


                                {{-- ANTERIOR --}}

                                @if($colonias->onFirstPage())

                                    <li class="page-item disabled">

                                        <span class="page-link">

                                            <i class="bi bi-chevron-left"></i>

                                        </span>

                                    </li>

                                @else

                                    <li class="page-item">

                                        <a class="page-link"
                                           href="{{ $colonias->previousPageUrl() }}"
                                           aria-label="Anterior">

                                            <i class="bi bi-chevron-left"></i>

                                        </a>

                                    </li>

                                @endif


                                {{-- PRIMERA PÁGINA --}}

                                @if($colonias->currentPage() > 3)

                                    <li class="page-item">

                                        <a class="page-link"
                                           href="{{ $colonias->url(1) }}">

                                            1

                                        </a>

                                    </li>


                                    @if($colonias->currentPage() > 4)

                                        <li class="page-item disabled">

                                            <span class="page-link">

                                                ...

                                            </span>

                                        </li>

                                    @endif

                                @endif


                                {{-- PÁGINAS CERCANAS --}}

                                @foreach($colonias->getUrlRange(
                                    max(1, $colonias->currentPage() - 2),
                                    min(
                                        $colonias->lastPage(),
                                        $colonias->currentPage() + 2
                                    )
                                ) as $page => $url)

                                    @if($page == $colonias->currentPage())

                                        <li class="page-item active"
                                            aria-current="page">

                                            <span class="page-link">

                                                {{ $page }}

                                            </span>

                                        </li>

                                    @else

                                        <li class="page-item">

                                            <a class="page-link"
                                               href="{{ $url }}">

                                                {{ $page }}

                                            </a>

                                        </li>

                                    @endif

                                @endforeach


                                {{-- ÚLTIMA PÁGINA --}}

                                @if($colonias->currentPage() < $colonias->lastPage() - 2)

                                    @if($colonias->currentPage() < $colonias->lastPage() - 3)

                                        <li class="page-item disabled">

                                            <span class="page-link">

                                                ...

                                            </span>

                                        </li>

                                    @endif


                                    <li class="page-item">

                                        <a class="page-link"
                                           href="{{ $colonias->url($colonias->lastPage()) }}">

                                            {{ $colonias->lastPage() }}

                                        </a>

                                    </li>

                                @endif


                                {{-- SIGUIENTE --}}

                                @if($colonias->hasMorePages())

                                    <li class="page-item">

                                        <a class="page-link"
                                           href="{{ $colonias->nextPageUrl() }}"
                                           aria-label="Siguiente">

                                            <i class="bi bi-chevron-right"></i>

                                        </a>

                                    </li>

                                @else

                                    <li class="page-item disabled">

                                        <span class="page-link">

                                            <i class="bi bi-chevron-right"></i>

                                        </span>

                                    </li>

                                @endif

                            </ul>

                        </nav>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection


{{-- =============================================================
     ESTILOS
     ============================================================= --}}

@push('styles')

<style>

    /* =========================================================
       PAGINACIÓN
       ========================================================= */

    .colonias-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }


    .colonias-pagination .page-item {
        margin: 0;
    }


    .colonias-pagination .page-link {

        min-width: 38px;
        height: 38px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0 10px;

        font-size: 0.875rem;
        line-height: 1;

        color: #495057;

        background-color: #fff;

        border: 1px solid #dee2e6;

        border-radius: 8px !important;

        text-decoration: none;

        transition:
            background-color .15s ease,
            border-color .15s ease,
            color .15s ease;

    }


    .colonias-pagination .page-link:hover {

        color: #0d6efd;

        background-color: #f8f9fa;

        border-color: #b6d4fe;

    }


    .colonias-pagination .page-item.active .page-link {

        color: #fff;

        background-color: #0d6efd;

        border-color: #0d6efd;

        font-weight: 600;

    }


    .colonias-pagination .page-item.disabled .page-link {

        color: #adb5bd;

        background-color: #f8f9fa;

        border-color: #dee2e6;

        cursor: not-allowed;

    }


    .colonias-pagination i {

        font-size: 0.85rem;

        line-height: 1;

    }


    /* =========================================================
       MÓVIL
       ========================================================= */

    @media (max-width: 575.98px) {

        .colonias-pagination {

            justify-content: center;

            flex-wrap: wrap;

            gap: 3px;

        }


        .colonias-pagination .page-link {

            min-width: 34px;

            height: 34px;

            padding: 0 8px;

            font-size: 0.8rem;

            border-radius: 7px !important;

        }

    }

</style>

@endpush