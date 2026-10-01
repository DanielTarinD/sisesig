@extends('layouts.app')

@section('title', 'Reporte global')

@push('styles')
<style>
    .report-summary {
        border-radius: 14px;
        border: 0;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }

    .summary-number {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .report-mobile-item {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    .report-mobile-item:last-child {
        border-bottom: 0;
    }

    .report-date {
        font-weight: 600;
    }

    .report-time {
        font-size: .8rem;
        color: #6c757d;
    }

    @media (max-width: 767.98px) {

        .report-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .5rem;
        }

        .report-actions .btn {
            width: 100%;
        }

        .summary-number {
            font-size: 1.5rem;
        }
    }

    @media print {

        .no-print {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endpush

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

    <div>
        <h2 class="fw-bold mb-1">
            Reporte global
        </h2>

        <p class="text-muted mb-0">
            Pacientes atendidos por rango de fecha.
        </p>
    </div>

</div>

{{-- Filtros --}}
<form class="card p-4 mb-4 no-print"
      method="GET"
      action="{{ route('reports.global') }}">

    <div class="row g-3 align-items-end">

        <div class="col-12 col-md-4">

            <label class="form-label fw-semibold">
                Desde
            </label>

            <input type="date"
                   name="from"
                   class="form-control"
                   value="{{ $from }}">

        </div>

        <div class="col-12 col-md-4">

            <label class="form-label fw-semibold">
                Hasta
            </label>

            <input type="date"
                   name="to"
                   class="form-control"
                   value="{{ $to }}">

        </div>

        <div class="col-12 col-md-4 report-actions">

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-search me-1"></i>
                Consultar

            </button>

            <a href="{{ route('reports.global.csv', ['from' => $from, 'to' => $to]) }}"
               class="btn btn-success">

                <i class="bi bi-filetype-csv me-1"></i>
                CSV

            </a>

            <a href="{{ route('reports.global.excel', ['from' => $from, 'to' => $to]) }}"
               class="btn btn-success">

                <i class="bi bi-file-earmark-excel me-1"></i>
                Excel

            </a>

            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="window.print()">

                <i class="bi bi-printer me-1"></i>
                Imprimir

            </button>

        </div>

    </div>

</form>

{{-- Resumen --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-md-6">

        <div class="card report-summary h-100">

            <div class="card-body">

                <div class="text-muted small mb-2">
                    ATENCIONES
                </div>

                <div class="summary-number">
                    {{ number_format($totalFollowUps) }}
                </div>

                <div class="text-muted small mt-2">
                    Registros encontrados
                </div>

            </div>

        </div>

    </div>

    <div class="col-12 col-md-6">

        <div class="card report-summary h-100">

            <div class="card-body">

                <div class="text-muted small mb-2">
                    PACIENTES ÚNICOS
                </div>

                <div class="summary-number">
                    {{ number_format($totalPatients) }}
                </div>

                <div class="text-muted small mt-2">
                    Pacientes con atención en el periodo
                </div>

            </div>

        </div>

    </div>

</div>

{{-- Resultados --}}
<div class="card">

    <div class="p-3 border-bottom d-flex flex-column flex-md-row justify-content-between gap-2">

        <div>
            <strong>
                Resultados
            </strong>

            @if($from || $to)

                <div class="small text-muted mt-1">

                    Periodo:

                    {{ $from ? \Carbon\Carbon::parse($from)->format('d/m/Y') : 'inicio' }}

                    —

                    {{ $to ? \Carbon\Carbon::parse($to)->format('d/m/Y') : 'actualidad' }}

                </div>

            @else

                <div class="small text-muted mt-1">
                    Todas las atenciones registradas
                </div>

            @endif

        </div>

        <div class="text-muted">

            {{ number_format($totalFollowUps) }}

            {{ $totalFollowUps === 1 ? 'atención' : 'atenciones' }}

        </div>

    </div>

    {{-- Escritorio --}}
    <div class="table-responsive d-none d-md-block">

        <table class="table table-striped table-hover mb-0">

            <thead>

                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Nombre</th>
                </tr>

            </thead>

            <tbody>

                @forelse($followUps as $f)

                    <tr>

                        <td>
                            {{ $f->date->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $f->date->format('H:i') }}
                        </td>

                        <td class="fw-semibold">
                            {{ $f->patient->name }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3"
                            class="text-center py-5 text-muted">

                            <i class="bi bi-search fs-2 d-block mb-2"></i>

                            No hay atenciones en el rango seleccionado.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Móvil --}}
    <div class="d-md-none">

        @forelse($followUps as $f)

            <div class="report-mobile-item">

                <div class="d-flex justify-content-between align-items-start gap-3">

                    <div>

                        <div class="report-date">
                            {{ $f->date->format('d/m/Y') }}
                        </div>

                        <div class="report-time">
                            {{ $f->date->format('H:i') }}
                        </div>

                    </div>

                    <div class="text-end fw-semibold">
                        {{ $f->patient->name }}
                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-5 text-muted">

                <i class="bi bi-search fs-2 d-block mb-2"></i>

                No hay atenciones en el rango seleccionado.

            </div>

        @endforelse

    </div>

</div>

@endsection