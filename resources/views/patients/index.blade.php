@extends('layouts.app')

@section('title', 'Pacientes')


@push('styles')

<style>

/* =========================================================
   TARJETAS MÓVILES
   ========================================================= */

.patient-mobile-card {

    border-bottom: 1px solid #e9ecef;

    padding: 1rem;

}


.patient-mobile-card:last-child {

    border-bottom: 0;

}


.patient-name {

    font-weight: 700;

    line-height: 1.2;

}


.patient-data {

    font-size: .88rem;

    color: #6c757d;

}


.patient-data strong {

    color: #495057;

}


.patient-open {

    min-width: 85px;

}


/* =========================================================
   FILTROS
   ========================================================= */

.patient-search {

    border-radius: 14px;

}


.patient-search-label {

    font-size: .82rem;

    font-weight: 600;

    color: #495057;

    margin-bottom: .35rem;

}


.patient-search .form-control,
.patient-search .form-select {

    min-height: 44px;

    border-radius: 10px;

}


.patient-search .form-control:focus,
.patient-search .form-select:focus {

    border-color: #86b7fe;

    box-shadow:
        0 0 0 .15rem rgba(13, 110, 253, .10);

}


/* =========================================================
   RESULTADOS
   ========================================================= */

.patient-results-count {

    font-size: .85rem;

    color: #6c757d;

}


/* =========================================================
   ESCRITORIO
   ========================================================= */

@media (min-width: 768px) {

    .patient-search-actions {

        display: flex;

        align-items: end;

        gap: .5rem;

    }

}


/* =========================================================
   MÓVIL
   ========================================================= */

@media (max-width: 767.98px) {

    .patients-heading {

        align-items: stretch !important;

    }


    .patients-heading .btn {

        width: 100%;

    }


    .patient-search {

        padding: .9rem !important;

    }


    .patient-search-actions {

        display: flex;

        flex-direction: column;

        gap: .5rem;

    }


    .patient-search-actions .btn {

        width: 100%;

    }


    .patient-search .form-control,
    .patient-search .form-select {

        min-height: 48px;

        font-size: 16px;

    }


    .patient-name {

        max-width: 65vw;

        word-break: break-word;

    }

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

<div class="card">


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

<div class="mt-3">

    {{ $patients->links() }}

</div>


@endsection