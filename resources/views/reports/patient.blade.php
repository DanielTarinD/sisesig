@extends('layouts.app')

@section('title', 'Reporte — ' . $patient->name)

@section('content')

    <div class="d-flex justify-content-between align-items-start mb-4 no-print">

        <div>
            <h2 class="fw-bold">
                Reporte del paciente
            </h2>

            <p class="text-muted mb-0">
                {{ $patient->name }}
            </p>
        </div>

        <button class="btn btn-dark" onclick="window.print()">
            <i class="bi bi-printer"></i>
            Imprimir
        </button>

    </div>

    {{-- Información del paciente --}}
    <div class="card p-4 mb-4">

        <h4 class="fw-bold">
            {{ $patient->name }}
        </h4>

        <div class="row mt-3">

            <div class="col-md-3">
                <strong>Fecha de nacimiento:</strong><br>

                {{ $patient->date_of_birth?->format('d/m/Y') ?? '—' }}
            </div>

            <div class="col-md-2">
                <strong>Edad:</strong><br>

                @if ($patient->date_of_birth)
                    {{ $patient->date_of_birth->age }} años
                @else
                    —
                @endif
            </div>

            <div class="col-md-3">
                <strong>Primera vez:</strong><br>

                {{ $patient->first_visit_date?->format('d/m/Y') ?? '—' }}
            </div>

            <div class="col-md-4">
                <strong>Colonia:</strong><br>

                {{ $patient->colonia ?: '—' }}
            </div>

            {{-- =========================================================
     SEXO
     ========================================================= --}}

            <div class="col-md-3 mt-3">
                <strong>Sexo:</strong><br>
                {{ $patient->gender ?: '—' }}
            </div>


            {{-- =========================================================
     TRATAMIENTOS
     ========================================================= --}}

            <div class="col-12 mt-3">
                <strong class="d-block mb-2">Tratamientos para enfermedades crónicas:</strong>

                <div class="row g-3">
                    {{-- DIABETES --}}
                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong>Diabetes Mellitus</strong>

                                @if ($patient->diabetes_treatment === null)
                                    No Sabe
                                @elseif ($patient->diabetes_treatment)
                                    En tratamiento
                                @else
                                    Sin tratamiento
                                @endif
                            </div>

                            <div>
                                <strong>Fecha de inicio:</strong>
                                @if ($patient->diabetes_treatment)
                                    {{ $patient->diabetes_treatment_start_date?->format('d/m/Y') ?? 'Sin fecha registrada' }}
                                @else
                                    No aplica
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- HIPERTENSIÓN --}}
                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong>Hipertensión Arterial</strong>

                                @if ($patient->hypertension_treatment === null)
                                    No Sabe
                                @elseif ($patient->hypertension_treatment)
                                    En tratamiento
                                @else
                                    Sin tratamiento
                                @endif
                            </div>

                            <div>
                                <strong>Fecha de inicio:</strong>
                                @if ($patient->hypertension_treatment)
                                    {{ $patient->hypertension_treatment_start_date?->format('d/m/Y') ?? 'Sin fecha registrada' }}
                                @else
                                    No aplica
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- =========================================================
     COMORBILIDADES
     ========================================================= --}}

            <div class="col-12 mt-3">

                <strong>Comorbilidades:</strong>

                {{ $patient->comorbidities->pluck('name')->join(', ') ?: 'Ninguna registrada' }}

            </div>

        </div>

    </div>

    {{-- Seguimiento histórico --}}
    <div class="card">

        <div class="p-4 border-bottom">

            <h5 class="fw-bold mb-0">
                Seguimiento histórico
            </h5>

        </div>

        <div class="table-responsive">

            <table class="table table-striped mb-0">

                <thead>

                    <tr>
                        <th>Fecha</th>
                        <th>TA</th>
                        <th>FR</th>
                        <th>Pulso</th>
                        <th>SpO2</th>
                        <th>Temp.</th>
                        <th>Sin ayuno</th>
                        <th>Glucemia</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($patient->followUps as $f)
                        <tr>

                            <td>
                                {{ $f->date->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $f->blood_pressure }}
                            </td>

                            <td>
                                {{ $f->respiratory_rate }}
                            </td>

                            <td>
                                {{ $f->pulse }}
                            </td>

                            <td>
                                {{ $f->spo2 }}
                            </td>

                            <td>
                                {{ $f->temperature }}
                            </td>

                            <td>
                                {{ $f->without_fasting ? 'No' : 'Si' }}
                            </td>

                            <td>
                                {{ $f->capillary_glucose }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="text-center py-4">

                                Sin registros.

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
