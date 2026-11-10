@extends('layouts.app')

@section('title', 'Modificar atención')

@section('content')
    <style>
        .followup-edit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .followup-edit-title {
            font-size: 1.65rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        .followup-edit-subtitle {
            color: #6c757d;
            margin-top: .25rem;
        }

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

        .capture-check-card input:checked~i {
            opacity: 1;
        }

        .capture-actions {
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }

        @media (max-width: 767.98px) {
            .followup-edit-header {
                flex-direction: column;
            }

            .followup-edit-title {
                font-size: 1.35rem;
            }

            .capture-section-title {
                padding: .9rem 1rem;
            }

            .capture-section-body {
                padding: 1rem;
            }

            .capture-actions {
                flex-direction: column-reverse;
            }

            .capture-actions .btn {
                width: 100%;
                min-height: 48px;
            }
        }
    </style>

    <div class="followup-edit-header">
        <div class="min-width-0">
            <h2 class="followup-edit-title">
                <i class="bi bi-pencil-square me-1"></i>
                Modificar atención
            </h2>
            <div class="followup-edit-subtitle">
                Paciente: <strong>{{ $followUp->patient->name }}</strong>
            </div>
        </div>

        <a href="{{ route('patients.show', $followUp->patient_id) }}" class="btn btn-outline-secondary no-print">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al expediente
        </a>
    </div>

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

    <div class="capture-section">
        <div class="capture-section-title">
            <div class="capture-icon">
                <i class="bi bi-activity"></i>
            </div>
            <div>
                <h5 class="mb-0">Datos de la atención</h5>
                <small class="text-muted">Modifique los valores registrados en el seguimiento</small>
            </div>
        </div>

        <div class="capture-section-body">
            <form method="POST" action="{{ route('followups.update', $followUp) }}" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="date" class="form-label followup-label">
                            Fecha de atención <span class="text-danger">*</span>
                        </label>
                        <input type="date" id="date" name="date" class="form-control followup-input"
                            value="{{ old('date', $followUp->date->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}"
                            required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="time" class="form-label followup-label">
                            Hora de atención <span class="text-danger">*</span>
                        </label>
                        <input type="time" id="time" name="time" class="form-control followup-input"
                            value="{{ old('time', $followUp->date->format('H:i')) }}" required>
                    </div>

                    @foreach ([
            'blood_pressure' => ['label' => 'Tensión arterial', 'placeholder' => '120/80', 'inputmode' => 'text'],
            'respiratory_rate' => ['label' => 'Frecuencia respiratoria', 'placeholder' => '18', 'inputmode' => 'numeric'],
            'pulse' => ['label' => 'Pulso', 'placeholder' => '72', 'inputmode' => 'numeric'],
            'spo2' => ['label' => 'SpO₂', 'placeholder' => '98', 'inputmode' => 'numeric'],
            'temperature' => ['label' => 'Temperatura', 'placeholder' => '36.5', 'inputmode' => 'decimal'],
        ] as $field => $config)
                        <div class="col-12 col-md-4">
                            <label for="{{ $field }}" class="form-label followup-label">
                                {{ $config['label'] }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="{{ $field }}" name="{{ $field }}"
                                class="form-control followup-input" placeholder="{{ $config['placeholder'] }}"
                                inputmode="{{ $config['inputmode'] }}" maxlength="50"
                                value="{{ old($field, $followUp->{$field}) }}" required>
                        </div>
                    @endforeach

                    <div class="col-12 col-md-4">
                        <label for="capillary_glucose" class="form-label followup-label">
                            Glucemia capilar <small class="text-muted fw-normal">(opcional)</small>
                        </label>
                        <input type="text" id="capillary_glucose" name="capillary_glucose"
                            class="form-control followup-input" placeholder="100" inputmode="numeric" maxlength="50"
                            value="{{ old('capillary_glucose', $followUp->capillary_glucose) }}">
                    </div>

                    <div class="col-12">
                        <label class="capture-check-card" for="without_fasting">
                            <input type="hidden" name="without_fasting" value="0">
                            <input class="form-check-input" type="checkbox" name="without_fasting" value="1"
                                id="without_fasting"
                                {{ old('without_fasting', $followUp->without_fasting) ? 'checked' : '' }}>
                            <span>
                                <strong>Sin ayuno</strong>
                                <small class="d-block text-muted">
                                    Marque si la glucemia fue tomada sin ayuno.
                                </small>
                            </span>
                            <i class="bi bi-check-circle-fill"></i>
                        </label>
                    </div>
                </div>

                <div class="capture-actions no-print">
                    <a href="{{ route('patients.show', $followUp->patient_id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
