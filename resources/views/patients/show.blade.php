@extends('layouts.app')

@section('title', $patient->name)

@section('content')

<style>
    /* =========================================================
       ENCABEZADO
       ========================================================= */

    .patient-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .patient-header-name {
        font-size: 1.65rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
        word-break: break-word;
    }

    .patient-header-subtitle {
        color: #6c757d;
        margin-top: .25rem;
    }

    .patient-header-actions {
        display: flex;
        gap: .5rem;
        flex-shrink: 0;
    }


    /* =========================================================
       SECCIONES
       ========================================================= */

    .capture-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .capture-section-title {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .capture-section-title h5 {
        font-weight: 700;
    }

    .capture-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9f5ee;
        color: #198754;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .capture-section-body {
        padding: 1.25rem;
    }


    /* =========================================================
       RESUMEN DEL PACIENTE
       ========================================================= */

    .patient-summary-item {
        min-height: 58px;
    }

    .patient-summary-label {
        display: block;
        color: #6c757d;
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: .25rem;
    }

    .patient-summary-value {
        font-weight: 600;
        color: #212529;
    }

    .comorbidity-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: .75rem 1rem;
    }


    /* =========================================================
       CAPTURA
       ========================================================= */

    .followup-input {
        min-height: 50px;
        font-size: 1rem;
        border-radius: 10px;
    }

    .followup-label {
        font-weight: 600;
        margin-bottom: .4rem;
    }

    .capture-check-card {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .9rem 1rem;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        cursor: pointer;
        background: #fff;
        transition: .15s ease;
    }

    .capture-check-card:hover {
        background: #f8f9fa;
    }

    .capture-check-card input {
        width: 1.25rem;
        height: 1.25rem;
        margin: 0;
        flex-shrink: 0;
    }

    .capture-check-card i {
        margin-left: auto;
        color: #198754;
        font-size: 1.2rem;
        opacity: .25;
    }

    .capture-check-card input:checked ~ i {
        opacity: 1;
    }

    .capture-check-card strong {
        display: block;
    }


    /* =========================================================
       HISTÓRICO
       ========================================================= */

    .followup-history-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .followup-history-count {
        color: #6c757d;
        font-size: .875rem;
        margin-top: .2rem;
    }

    .followup-desktop table {
        margin-bottom: 0;
    }

    .followup-desktop th {
        white-space: nowrap;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .02em;
        color: #6c757d;
        background: #f8f9fa;
    }

    .followup-desktop td {
        vertical-align: middle;
        white-space: nowrap;
    }


    /* =========================================================
       MÓVIL
       ========================================================= */

    .followup-mobile {
        display: none;
    }

    .followup-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    }

    .followup-card:last-child {
        margin-bottom: 0;
    }

    .followup-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .followup-date {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
    }

    .followup-date i {
        color: #198754;
    }

    .followup-data {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .followup-data-item {
        padding: 11px 14px;
        border-bottom: 1px solid #f0f0f0;
    }

    .followup-data-label {
        display: block;
        font-size: .7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .followup-data-value {
        font-weight: 600;
        color: #212529;
    }

    .followup-delete {
        padding: 0 14px 14px;
    }


    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 767.98px) {

        .patient-header {
            flex-direction: column;
        }

        .patient-header-name {
            font-size: 1.35rem;
        }

        .patient-header-actions {
            width: 100%;
        }

        .patient-header-actions .btn {
            flex: 1;
        }

        .capture-section-title {
            padding: .9rem 1rem;
        }

        .capture-section-body {
            padding: 1rem;
        }

        .patient-summary-item {
            min-height: auto;
        }

        .followup-desktop {
            display: none;
        }

        .followup-mobile {
            display: block;
            padding: .5rem;
        }

        .followup-history-header {
            padding: 1rem;
        }

        .followup-card-header {
            padding: 12px;
        }

        .followup-data-item {
            padding: 10px 12px;
        }

        .followup-data-value {
            font-size: .95rem;
        }

        .mobile-hide-text {
            display: none;
        }
    }

    @media (min-width: 768px) {
        .mobile-hide-text {
            display: inline;
        }
    }


    /* =========================================================
       IMPRESIÓN
       ========================================================= */

    @media print {

        .no-print {
            display: none !important;
        }

        .capture-section {
            border: 1px solid #ccc;
            box-shadow: none;
        }

        .followup-mobile {
            display: none !important;
        }

        .followup-desktop {
            display: block !important;
        }

        body {
            background: #fff !important;
        }
    }
</style>


{{-- =========================================================
     ENCABEZADO
     ========================================================= --}}

<div class="patient-header">

    <div class="min-width-0">
        <h2 class="patient-header-name">
            {{ $patient->name }}
        </h2>

        <div class="patient-header-subtitle">
            Expediente del paciente
        </div>
    </div>

    <div class="patient-header-actions no-print">

        <a href="{{ route('reports.patient', $patient) }}"
           class="btn btn-outline-dark">
            <i class="bi bi-printer"></i>
            <span class="mobile-hide-text ms-1">Reporte</span>
        </a>

        @if(auth()->user()->isCapturista())

            <a href="{{ route('patients.edit', $patient) }}"
               class="btn btn-primary">
                <i class="bi bi-pencil"></i>
                <span class="mobile-hide-text ms-1">Editar</span>
            </a>

        @endif

    </div>

</div>


{{-- =========================================================
     EXPEDIENTE
     ========================================================= --}}

<div class="capture-section">

    <div class="capture-section-title">

        <div class="capture-icon">
            <i class="bi bi-person-vcard"></i>
        </div>

        <div>
            <h5 class="mb-0">Expediente</h5>
            <small class="text-muted">
                Información general del paciente
            </small>
        </div>

    </div>

    <div class="capture-section-body">

        <div class="row g-4">

            <div class="col-6 col-md-3">

                <div class="patient-summary-item">

                    <span class="patient-summary-label">
                        Fecha de nacimiento
                    </span>

                    <div class="patient-summary-value">

                        {{ $patient->date_of_birth?->format('d/m/Y') ?? '—' }}

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-3">

                <div class="patient-summary-item">

                    <span class="patient-summary-label">
                        Edad
                    </span>

                    <div class="patient-summary-value">

                        @if($patient->date_of_birth)
                            {{ $patient->date_of_birth->age }} años
                        @else
                            —
                        @endif

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-3">

                <div class="patient-summary-item">

                    <span class="patient-summary-label">
                        Primera vez
                    </span>

                    <div class="patient-summary-value">

                        {{ $patient->first_visit_date?->format('d/m/Y') ?? '—' }}

                    </div>

                </div>

            </div>


            <div class="col-6 col-md-3">

                <div class="patient-summary-item">

                    <span class="patient-summary-label">
                        Colonia
                    </span>

                    <div class="patient-summary-value">

                        {{ $patient->colonia ?: '—' }}

                    </div>

                </div>

            </div>


            <div class="col-12">

                <span class="patient-summary-label">
                    Comorbilidades
                </span>

                <div class="comorbidity-box">

                    <span class="patient-summary-value">

                        {{ $patient->comorbidities->pluck('name')->join(', ') ?: 'Ninguna registrada' }}

                    </span>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     NUEVO SEGUIMIENTO
     ========================================================= --}}

@if(auth()->user()->isCapturista())

<div class="capture-section no-print">

    <div class="capture-section-title">

        <div class="capture-icon">
            <i class="bi bi-activity"></i>
        </div>

        <div>
            <h5 class="mb-0">
                Nuevo seguimiento
            </h5>

            <small class="text-muted">
                La fecha y hora se registran automáticamente
            </small>
        </div>

    </div>


    <div class="capture-section-body">

        @if ($errors->any())

            <div class="alert alert-danger">

                <strong>Revise los siguientes campos:</strong>

                <ul class="mb-0 mt-2">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        <form method="POST"
              action="{{ route('followups.store', $patient) }}"
              autocomplete="off">

            @csrf


            <div class="row g-3">

                {{-- TENSIÓN ARTERIAL --}}
                <div class="col-6 col-md-4">

                    <label class="form-label followup-label"
                           for="blood_pressure">

                        Tensión arterial
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        name="blood_pressure"
                        id="blood_pressure"
                        class="form-control followup-input"
                        placeholder="120/80"
                        inputmode="text"
                        autocomplete="off"
                        value="{{ old('blood_pressure') }}"
                        required>

                </div>


                {{-- FRECUENCIA RESPIRATORIA --}}
                <div class="col-6 col-md-4">

                    <label class="form-label followup-label"
                           for="respiratory_rate">

                        Frecuencia respiratoria
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        name="respiratory_rate"
                        id="respiratory_rate"
                        class="form-control followup-input"
                        placeholder="18"
                        inputmode="numeric"
                        autocomplete="off"
                        value="{{ old('respiratory_rate') }}"
                        required>

                </div>


                {{-- PULSO --}}
                <div class="col-6 col-md-4">

                    <label class="form-label followup-label"
                           for="pulse">

                        Pulso
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        name="pulse"
                        id="pulse"
                        class="form-control followup-input"
                        placeholder="72"
                        inputmode="numeric"
                        autocomplete="off"
                        value="{{ old('pulse') }}"
                        required>

                </div>


                {{-- SPO2 --}}
                <div class="col-6 col-md-4">

                    <label class="form-label followup-label"
                           for="spo2">

                        SpO2
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        name="spo2"
                        id="spo2"
                        class="form-control followup-input"
                        placeholder="98"
                        inputmode="numeric"
                        autocomplete="off"
                        value="{{ old('spo2') }}"
                        required>

                </div>


                {{-- TEMPERATURA --}}
                <div class="col-6 col-md-4">

                    <label class="form-label followup-label"
                           for="temperature">

                        Temperatura
                        <span class="text-danger">*</span>

                    </label>

                    <input
                        name="temperature"
                        id="temperature"
                        class="form-control followup-input"
                        placeholder="36.5"
                        inputmode="decimal"
                        autocomplete="off"
                        value="{{ old('temperature') }}"
                        required>

                </div>


                {{-- GLUCEMIA --}}
                <div class="col-6 col-md-4">

                    <label class="form-label followup-label"
                           for="capillary_glucose">

                        Glucemia capilar
                        <small class="text-muted fw-normal">
                            (opcional)
                        </small>

                    </label>

                    <input
                        name="capillary_glucose"
                        id="capillary_glucose"
                        class="form-control followup-input"
                        placeholder="100"
                        inputmode="numeric"
                        autocomplete="off"
                        value="{{ old('capillary_glucose') }}">

                </div>


                {{-- SIN AYUNO --}}
                <div class="col-12">

                    <label class="capture-check-card"
                           for="without_fasting">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="without_fasting"
                            value="1"
                            id="without_fasting"
                            {{ old('without_fasting') ? 'checked' : '' }}>

                        <span>

                            <strong>
                                Sin ayuno
                            </strong>

                            <small class="d-block text-muted">
                                Marque si la glucemia fue tomada sin ayuno.
                            </small>

                        </span>

                        <i class="bi bi-check-circle-fill"></i>

                    </label>

                </div>

            </div>


            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-success w-100"
                    style="min-height:54px;border-radius:12px;font-weight:600">

                    <i class="bi bi-plus-circle me-1"></i>

                    Registrar seguimiento

                </button>

            </div>

        </form>

    </div>

</div>

@endif


{{-- =========================================================
     HISTÓRICO
     ========================================================= --}}

<div class="card">

    <div class="followup-history-header">

        <h5 class="fw-bold mb-0">
            Histórico de seguimiento
        </h5>

        @if($patient->followUps->count() > 0)

            <div class="followup-history-count">

                {{ $patient->followUps->count() }}

                {{ $patient->followUps->count() == 1
                    ? 'atención registrada'
                    : 'atenciones registradas' }}

            </div>

        @else

            <div class="followup-history-count">
                No hay atenciones registradas.
            </div>

        @endif

    </div>


    {{-- =====================================================
         DESKTOP / TABLET
         ===================================================== --}}

    <div class="followup-desktop table-responsive">

        <table class="table table-striped table-hover">

            <thead>

                <tr>

                    <th>Fecha</th>
                    <th>TA</th>
                    <th>FR</th>
                    <th>Pulso</th>
                    <th>SpO2</th>
                    <th>Temp.</th>
                    <th>Ayuno</th>
                    <th>Glucemia</th>
                    <th class="no-print"></th>

                </tr>

            </thead>

            <tbody>

                @forelse($patient->followUps as $f)

                    <tr>

                        <td class="fw-semibold">
                            {{ $f->date->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            {{ $f->blood_pressure ?: '—' }}
                        </td>

                        <td>
                            {{ $f->respiratory_rate ?: '—' }}
                        </td>

                        <td>
                            {{ $f->pulse ?: '—' }}
                        </td>

                        <td>
                            {{ $f->spo2 ?: '—' }}
                        </td>

                        <td>
                            {{ $f->temperature ?: '—' }}
                        </td>

                        <td>
                            {{ $f->without_fasting ? 'No' : 'Si' }}
                        </td>

                        <td>
                            {{ $f->capillary_glucose ?: '—' }}
                        </td>

                        <td class="no-print">

                            @if(auth()->user()->isCapturista())

                                <form
                                    method="POST"
                                    action="{{ route('followups.destroy', $f) }}"
                                    onsubmit="return confirm('¿Eliminar este seguimiento?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9"
                            class="text-center py-5 text-muted">

                            <i class="bi bi-clipboard2-x fs-2 d-block mb-2"></i>

                            Sin seguimientos registrados.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         MÓVIL
         ===================================================== --}}

    <div class="followup-mobile">

        @forelse($patient->followUps as $f)

            <div class="followup-card">

                <div class="followup-card-header">

                    <div class="followup-date">

                        <i class="bi bi-calendar3"></i>

                        <span>
                            {{ $f->date->format('d/m/Y') }}
                        </span>

                    </div>

                    <small class="text-muted">
                        {{ $f->date->format('H:i') }} h
                    </small>

                </div>


                <div class="followup-data">

                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            Tensión arterial
                        </span>

                        <span class="followup-data-value">
                            {{ $f->blood_pressure ?: '—' }}
                        </span>

                    </div>


                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            Pulso
                        </span>

                        <span class="followup-data-value">
                            {{ $f->pulse ?: '—' }}
                        </span>

                    </div>


                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            Frecuencia respiratoria
                        </span>

                        <span class="followup-data-value">
                            {{ $f->respiratory_rate ?: '—' }}
                        </span>

                    </div>


                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            SpO2
                        </span>

                        <span class="followup-data-value">
                            {{ $f->spo2 ?: '—' }}
                        </span>

                    </div>


                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            Temperatura
                        </span>

                        <span class="followup-data-value">
                            {{ $f->temperature ?: '—' }}
                        </span>

                    </div>


                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            Ayuno
                        </span>

                        <span class="followup-data-value">
                            {{ $f->without_fasting ? 'No' : 'Si' }}
                        </span>

                    </div>


                    <div class="followup-data-item">

                        <span class="followup-data-label">
                            Glucemia capilar
                        </span>

                        <span class="followup-data-value">
                            {{ $f->capillary_glucose ?: '—' }}
                        </span>

                    </div>

                </div>


                @if(auth()->user()->isCapturista())

                    <div class="followup-delete no-print">

                        <form
                            method="POST"
                            action="{{ route('followups.destroy', $f) }}"
                            onsubmit="return confirm('¿Eliminar este seguimiento?')">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-danger w-100">

                                <i class="bi bi-trash me-1"></i>

                                Eliminar seguimiento

                            </button>

                        </form>

                    </div>

                @endif

            </div>

        @empty

            <div class="text-center py-5 text-muted">

                <i class="bi bi-clipboard2-x fs-2 d-block mb-2"></i>

                Sin seguimientos registrados.

            </div>

        @endforelse

    </div>

</div>

@endsection