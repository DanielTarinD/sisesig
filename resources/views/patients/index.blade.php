@extends('layouts.app')

@section('title', 'Pacientes')


@push('styles')

<style>
/* Diseño renovado del catálogo de pacientes */
.patients-heading { padding: .35rem 0 .85rem; }
.patients-heading h2 { color: #172b4d; font-size: 1.75rem; letter-spacing: -.035em; }
.patients-heading p { color: #718096 !important; font-size: .94rem; }
.patients-heading .btn { border-radius: 10px; padding: .65rem 1rem; font-weight: 600; box-shadow: 0 3px 10px rgba(13,110,253,.12); }
.patient-search { background: #fff; border: 1px solid #e7edf4; border-radius: 16px; padding: 1.25rem !important; box-shadow: 0 5px 20px rgba(24, 46, 75, .045); }
.patient-search-label { color: #526176; font-size: .78rem; font-weight: 700; letter-spacing: .035em; text-transform: uppercase; margin-bottom: .5rem; }
.patient-search .form-control, .patient-search .form-select { border: 1px solid #dce4ed; border-radius: 10px; min-height: 43px; font-size: .92rem; background-color: #fbfcfe; }
.patient-search .form-control:focus, .patient-search .form-select:focus { background: #fff; border-color: #8ab9f7; box-shadow: 0 0 0 .22rem rgba(13,110,253,.10); }
.patient-search-actions { display: flex; align-items: end; gap: .5rem; height: 100%; }
.patient-search-actions .btn { border-radius: 10px; min-height: 43px; padding-inline: .9rem; font-weight: 600; white-space: nowrap; }
.patient-results-count { color: #64748b; font-size: .88rem; padding: .45rem .1rem .75rem; }
.patient-results-count strong { color: #1e293b; font-weight: 700; }
.patient-results-card { border: 1px solid #e5ebf2; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 22px rgba(24,46,75,.045); background: #fff; }
.patient-results-card .table { color: #334155; }
.patient-results-card .table thead th { background: #f5f8fc; color: #64748b; border-bottom: 1px solid #e5ebf2; font-size: .73rem; font-weight: 700; letter-spacing: .045em; text-transform: uppercase; padding: .95rem .85rem; white-space: nowrap; }
.patient-results-card .table tbody td { padding: .9rem .85rem; vertical-align: middle; border-color: #edf1f6; font-size: .88rem; }
.patient-results-card .table tbody tr { transition: background-color .15s ease; }
.patient-results-card .table tbody tr:hover { background: #f7faff; }
.patient-results-card .table tbody td:first-child { color: #1e293b; font-weight: 600; }
.patient-results-card .btn { border-radius: 8px; font-weight: 600; padding: .38rem .65rem; white-space: nowrap; }
.patient-mobile-card { border-bottom: 1px solid #edf1f6; padding: 1rem 1.1rem; background: #fff; }
.patient-mobile-card:last-child { border-bottom: 0; }
.patient-mobile-card:active { background: #f8fbff; }
.patient-name { color: #1e293b; font-size: 1rem; font-weight: 700; line-height: 1.3; }
.patient-data { color: #64748b; font-size: .83rem; line-height: 1.65; }
.patient-data strong { color: #475569; font-weight: 600; }
.patient-open { border-radius: 9px; min-width: 78px; font-weight: 600; }
/* Paginación simplificada: anterior | página actual | siguiente */
.patient-pagination { margin-top: 1.15rem !important; }
.patient-pagination-bar { display: flex; justify-content: center; align-items: center; gap: .85rem; flex-wrap: wrap; }
.patient-pagination-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; min-height: 38px; padding: .45rem .85rem; border: 1px solid #dbe3ed; border-radius: 9px; background: #fff; color: #334155; font-size: .85rem; font-weight: 600; text-decoration: none; transition: background-color .15s ease, border-color .15s ease; }
.patient-pagination-btn:hover { background: #f3f7fc; border-color: #b9c9dc; color: #1d4ed8; }
.patient-pagination-btn.is-disabled { background: #f8fafc; color: #a0aec0; border-color: #e8edf3; cursor: not-allowed; pointer-events: none; }
.patient-pagination-status { color: #64748b; font-size: .88rem; white-space: nowrap; }
.patient-pagination-status strong { color: #1e293b; font-weight: 700; }
@media (max-width: 420px) { .patient-pagination-bar { gap: .5rem; } .patient-pagination-btn { padding: .4rem .65rem; } .patient-pagination-status { font-size: .82rem; } }
@media (min-width: 768px) { .patient-search-actions { justify-content: flex-start; } }
@media (max-width: 767.98px) {
  .patients-heading { align-items: stretch !important; }
  .patients-heading h2 { font-size: 1.5rem; }
  .patients-heading .btn { width: 100%; }
  .patient-search { padding: 1rem !important; }
  .patient-search-actions { flex-direction: row; }
  .patient-search-actions .btn { flex: 1; }
  .patient-search .form-control, .patient-search .form-select { min-height: 46px; font-size: 16px; }
  .patient-name { max-width: 65vw; overflow-wrap: anywhere; }
}
</style>

@endpush


@section('content')


{{-- ======================================================
     ENCABEZADO
     ====================================================== --}}

<div
    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 patients-heading"
>

    <div>

        <h2 class="fw-bold mb-1">

            <i class="bi bi-people me-1"></i>

            Pacientes

        </h2>

        <p class="text-muted mb-0">

            Catálogo de expedientes.

        </p>

    </div>


    @if(auth()->user()->isCapturista())

        <a
            href="{{ route('patients.create') }}"
            class="btn btn-primary"
        >

            <i class="bi bi-person-plus me-1"></i>

            Nuevo paciente

        </a>

    @endif

</div>



{{-- ======================================================
     FILTROS
     ====================================================== --}}

<form
    class="card p-3 mb-3 patient-search"
    method="GET"
    action="{{ route('patients.index') }}"
>

    <div class="row g-3">


        {{-- Nombre --}}

        <div class="col-12 col-md-5">

            <label
                for="patient-search-name"
                class="patient-search-label"
            >

                <i class="bi bi-search me-1"></i>

                Nombre del paciente

            </label>


            <input
                type="text"
                name="q"
                id="patient-search-name"
                class="form-control"
                placeholder="Nombre o parte del nombre..."
                value="{{ $q }}"
                autocomplete="off"
            >

        </div>



        {{-- Colonia --}}

        <div class="col-12 col-md-3">

            <label
                for="patient-search-colonia"
                class="patient-search-label"
            >

                <i class="bi bi-geo-alt me-1"></i>

                Colonia

            </label>


            <select
                name="colonia"
                id="patient-search-colonia"
                class="form-select"
            >

                <option value="">
                    Todas las colonias
                </option>


                @foreach($colonias as $item)

                    <option
                        value="{{ $item->name }}"
                        @selected($colonia === $item->name)
                    >

                        {{ $item->name }}

                        @if(!$item->active)
                            (inactiva)
                        @endif

                    </option>

                @endforeach

            </select>

        </div>



        {{-- Fecha nacimiento --}}

        <div class="col-12 col-md-2">

            <label
                for="patient-search-date"
                class="patient-search-label"
            >

                <i class="bi bi-calendar3 me-1"></i>

                Nacimiento

            </label>


            <input
                type="date"
                name="date_of_birth"
                id="patient-search-date"
                class="form-control"
                value="{{ $dateOfBirth }}"
            >

        </div>



        {{-- Botones --}}

        <div class="col-12 col-md-2">

            <div class="patient-search-actions">


                <button
                    type="submit"
                    class="btn btn-dark"
                >

                    <i class="bi bi-search me-1"></i>

                    Buscar

                </button>


                @if(
                    $q !== '' ||
                    $colonia !== '' ||
                    $dateOfBirth !== ''
                )

                    <a
                        href="{{ route('patients.index') }}"
                        class="btn btn-outline-secondary"
                    >

                        <i class="bi bi-x-lg me-1"></i>

                        Limpiar

                    </a>

                @endif


            </div>

        </div>

    </div>

</form>



{{-- ======================================================
     RESUMEN
     ====================================================== --}}

<div class="d-flex justify-content-between align-items-center mb-2">

    <div class="patient-results-count">

        @if(
            $q !== '' ||
            $colonia !== '' ||
            $dateOfBirth !== ''
        )

            <i class="bi bi-funnel me-1"></i>

            Resultados encontrados:
            <strong>{{ $patients->total() }}</strong>

        @else

            <i class="bi bi-people me-1"></i>

            Pacientes registrados:
            <strong>{{ $patients->total() }}</strong>

        @endif

    </div>

</div>



{{-- ======================================================
     RESULTADOS
     ====================================================== --}}

<div class="card patient-results-card">


    {{-- ==================================================
         ESCRITORIO
         ================================================== --}}

    <div class="table-responsive d-none d-md-block">

        <table class="table table-hover mb-0">

            <thead>

                <tr>

                    <th>
                        Nombre
                    </th>

                    <th>
                        Fecha nacimiento
                    </th>

                    <th>
                        Edad
                    </th>

                    <th>
                        Primera vez
                    </th>

                    <th>
                        Colonia
                    </th>

                    <th>
                        Última atención
                    </th>

                    <th></th>

                </tr>

            </thead>


            <tbody>

            @forelse($patients as $patient)

                @php
                    $lastFollowUp = $patient->followUps->first();
                @endphp


                <tr>


                    <td class="fw-semibold">

                        {{ $patient->name }}

                    </td>


                    <td>

                        {{ $patient->date_of_birth?->format('d/m/Y') ?? '—' }}

                    </td>


                    <td>

                        @if($patient->date_of_birth)

                            {{ $patient->date_of_birth->age }}
                            años

                        @else

                            —

                        @endif

                    </td>


                    <td>

                        {{ $patient->first_visit_date?->format('d/m/Y') ?? '—' }}

                    </td>


                    <td>

                        {{ $patient->colonia ?: '—' }}

                    </td>


                    <td>

                        {{ $lastFollowUp?->date?->format('d/m/Y H:i') ?? 'Sin atención' }}

                    </td>


                    <td class="text-end">

                        <a
                            href="{{ route('patients.show', $patient) }}"
                            class="btn btn-sm btn-outline-primary"
                        >

                            <i class="bi bi-folder2-open me-1"></i>

                            Abrir

                        </a>

                    </td>


                </tr>


            @empty

                <tr>

                    <td
                        colspan="7"
                        class="text-center py-5 text-muted"
                    >

                        <i class="bi bi-person-x fs-2 d-block mb-2"></i>

                        @if(
                            $q !== '' ||
                            $colonia !== '' ||
                            $dateOfBirth !== ''
                        )

                            No se encontraron pacientes
                            con los filtros seleccionados.

                        @else

                            No hay pacientes.

                        @endif

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>



    {{-- ==================================================
         MÓVIL
         ================================================== --}}

    <div class="d-md-none">

        @forelse($patients as $patient)

            @php
                $lastFollowUp = $patient->followUps->first();
            @endphp


            <div class="patient-mobile-card">


                <div
                    class="d-flex justify-content-between align-items-start gap-3"
                >


                    <div class="min-width-0">


                        <div class="patient-name mb-2">

                            {{ $patient->name }}

                        </div>


                        <div class="patient-data">


                            <div class="mb-1">

                                <strong>
                                    Edad:
                                </strong>

                                @if($patient->date_of_birth)

                                    {{ $patient->date_of_birth->age }}
                                    años

                                @else

                                    —

                                @endif

                            </div>


                            <div class="mb-1">

                                <strong>
                                    Nacimiento:
                                </strong>

                                {{ $patient->date_of_birth?->format('d/m/Y') ?? '—' }}

                            </div>


                            <div class="mb-1">

                                <strong>
                                    Primera vez:
                                </strong>

                                {{ $patient->first_visit_date?->format('d/m/Y') ?? '—' }}

                            </div>


                            <div class="mb-1">

                                <strong>
                                    Colonia:
                                </strong>

                                {{ $patient->colonia ?: '—' }}

                            </div>


                            <div>

                                <strong>
                                    Última atención:
                                </strong>

                                {{ $lastFollowUp?->date?->format('d/m/Y H:i') ?? 'Sin atención' }}

                            </div>


                        </div>

                    </div>



                    <a
                        href="{{ route('patients.show', $patient) }}"
                        class="btn btn-sm btn-outline-primary patient-open flex-shrink-0"
                    >

                        <i class="bi bi-folder2-open"></i>

                        Abrir

                    </a>


                </div>


            </div>


        @empty


            <div class="text-center py-5 text-muted">

                <i class="bi bi-person-x fs-2 d-block mb-2"></i>


                @if(
                    $q !== '' ||
                    $colonia !== '' ||
                    $dateOfBirth !== ''
                )

                    No se encontraron pacientes
                    con los filtros seleccionados.

                @else

                    No hay pacientes.

                @endif

            </div>


        @endforelse

    </div>

</div>



{{-- ======================================================
     PAGINACIÓN
     ====================================================== --}}

<div class="mt-3 patient-pagination">

    @if($patients->lastPage() > 1)

        <div class="patient-pagination-bar" aria-label="Paginación de pacientes">

            @if($patients->onFirstPage())
                <span class="patient-pagination-btn is-disabled" aria-disabled="true">
                    <i class="bi bi-chevron-left"></i>
                    Anterior
                </span>
            @else
                <a class="patient-pagination-btn" href="{{ $patients->appends(request()->query())->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                    Anterior
                </a>
            @endif

            <span class="patient-pagination-status">
                Página <strong>{{ $patients->currentPage() }}</strong> de <strong>{{ $patients->lastPage() }}</strong>
            </span>

            @if($patients->hasMorePages())
                <a class="patient-pagination-btn" href="{{ $patients->appends(request()->query())->nextPageUrl() }}" rel="next">
                    Siguiente
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span class="patient-pagination-btn is-disabled" aria-disabled="true">
                    Siguiente
                    <i class="bi bi-chevron-right"></i>
                </span>
            @endif

        </div>

    @endif

</div>


@endsection