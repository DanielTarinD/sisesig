@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        height: 100%;
    }

    .dashboard-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 1.3rem;
    }

    .dashboard-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    .chart-row {
        display: grid;
        grid-template-columns: 90px 1fr 45px;
        align-items: center;
        gap: .75rem;
        margin-bottom: .9rem;
    }

    .chart-label {
        font-size: .85rem;
        color: #6c757d;
        text-align: right;
    }

    .chart-bar-container {
        height: 12px;
        background: #eef1f5;
        border-radius: 20px;
        overflow: hidden;
    }

    .chart-bar {
        height: 100%;
        background: #0d6efd;
        border-radius: 20px;
        min-width: 4px;
    }

    .chart-value {
        font-weight: 600;
        font-size: .85rem;
    }

    .period-buttons .btn {
        min-width: 90px;
    }

    @media (max-width: 767.98px) {
        .dashboard-number {
            font-size: 1.7rem;
        }

        .period-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            width: 100%;
        }

        .period-buttons .btn {
            width: 100%;
        }

        .chart-row {
            grid-template-columns: 70px 1fr 35px;
            gap: .5rem;
        }

        .chart-label {
            font-size: .75rem;
        }
    }
</style>
@endpush

@section('content')

<div class="container-fluid">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Dashboard
            </h2>

            <p class="text-muted mb-0">
                Resumen general del seguimiento de pacientes.
            </p>
        </div>

        {{-- Periodo --}}
        <div class="period-buttons btn-group" role="group">

            <a href="{{ route('dashboard', ['period' => 'today']) }}"
               class="btn {{ $period === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                Hoy
            </a>

            <a href="{{ route('dashboard', ['period' => 'week']) }}"
               class="btn {{ $period === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                Semana
            </a>

            <a href="{{ route('dashboard', ['period' => 'month']) }}"
               class="btn {{ $period === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                Mes
            </a>

            <a href="{{ route('dashboard', ['period' => 'year']) }}"
               class="btn {{ $period === 'year' ? 'btn-primary' : 'btn-outline-primary' }}">
                Año
            </a>

        </div>

    </div>

    {{-- Periodo seleccionado --}}
    <div class="alert alert-light border mb-4">

        <i class="bi bi-calendar3 me-2"></i>

        <strong>{{ $periodLabel }}</strong>

        <span class="text-muted">
            — {{ $startDate->format('d/m/Y') }}
            al {{ $endDate->format('d/m/Y') }}
        </span>

    </div>

    {{-- Indicadores --}}
    <div class="row g-3 mb-4">

        {{-- Pacientes --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <div class="text-muted small mb-2">
                                PACIENTES
                            </div>

                            <div class="dashboard-number">
                                {{ number_format($totalPatients) }}
                            </div>

                            <div class="text-muted small mt-2">
                                Total registrados
                            </div>
                        </div>

                        <div class="dashboard-icon bg-primary-subtle text-primary">
                            <i class="bi bi-people"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- Atenciones --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <div class="text-muted small mb-2">
                                ATENCIONES
                            </div>

                            <div class="dashboard-number">
                                {{ number_format($periodFollowUps) }}
                            </div>

                            <div class="text-muted small mt-2">
                                {{ $periodLabel }}
                            </div>
                        </div>

                        <div class="dashboard-icon bg-success-subtle text-success">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- Pacientes nuevos --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <div class="text-muted small mb-2">
                                PACIENTES NUEVOS
                            </div>

                            <div class="dashboard-number">
                                {{ number_format($newPatients) }}
                            </div>

                            <div class="text-muted small mt-2">
                                {{ $periodLabel }}
                            </div>
                        </div>

                        <div class="dashboard-icon bg-info-subtle text-info">
                            <i class="bi bi-person-plus"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- Atenciones hoy --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <div class="text-muted small mb-2">
                                ATENCIONES HOY
                            </div>

                            <div class="dashboard-number">
                                {{ number_format($todayFollowUps) }}
                            </div>

                            <div class="text-muted small mt-2">
                                Registradas hoy
                            </div>
                        </div>

                        <div class="dashboard-icon bg-warning-subtle text-warning">
                            <i class="bi bi-calendar-check"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    {{-- Segunda fila --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="text-muted small mb-2">
                        PACIENTES CON DM
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <div class="dashboard-number">
                            {{ number_format($diabetesPatients) }}
                        </div>

                        <i class="bi bi-heart-pulse fs-2 text-danger"></i>

                    </div>

                    <div class="text-muted small mt-2">
                        Diabetes Mellitus
                    </div>

                </div>
            </div>

        </div>

        <div class="col-12 col-md-4">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="text-muted small mb-2">
                        PACIENTES CON HAS
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <div class="dashboard-number">
                            {{ number_format($hypertensionPatients) }}
                        </div>

                        <i class="bi bi-heart-pulse fs-2 text-warning"></i>

                    </div>

                    <div class="text-muted small mt-2">
                        Hipertensión Arterial Sistémica
                    </div>

                </div>
            </div>

        </div>

        <div class="col-12 col-md-4">

            <div class="card dashboard-card">
                <div class="card-body">

                    <div class="text-muted small mb-2">
                        PROMEDIO DE ATENCIONES
                    </div>

                    <div class="d-flex align-items-center justify-content-between">

                        <div class="dashboard-number">
                            {{ number_format($averageFollowUps, 1) }}
                        </div>

                        <i class="bi bi-bar-chart fs-2 text-primary"></i>

                    </div>

                    <div class="text-muted small mt-2">
                        Atenciones por paciente
                    </div>

                </div>
            </div>

        </div>

    </div>

    {{-- Gráfica --}}
    <div class="card dashboard-card">

        <div class="card-body p-4">

            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">

                <div>
                    <h5 class="fw-bold mb-1">
                        Atenciones por día
                    </h5>

                    <p class="text-muted small mb-0">
                        {{ $periodLabel }}
                    </p>
                </div>

                <div class="text-muted small">
                    {{ number_format($periodFollowUps) }} atenciones
                </div>

            </div>

            @if($followUpsByDay->count())

                @php
                    $maxFollowUps = max($followUpsByDay->max('total'), 1);
                @endphp

                @foreach($followUpsByDay as $item)

                    @php
                        $percentage = ($item->total / $maxFollowUps) * 100;
                    @endphp

                    <div class="chart-row">

                        <div class="chart-label">
                            {{ \Carbon\Carbon::parse($item->day)->format('d/m') }}
                        </div>

                        <div class="chart-bar-container">
                            <div class="chart-bar"
                                 style="width: {{ $percentage }}%;">
                            </div>
                        </div>

                        <div class="chart-value">
                            {{ $item->total }}
                        </div>

                    </div>

                @endforeach

            @else

                <div class="text-center py-5 text-muted">

                    <i class="bi bi-bar-chart fs-1 d-block mb-3"></i>

                    No existen atenciones en el periodo seleccionado.

                </div>

            @endif

        </div>

    </div>

</div>

@endsection