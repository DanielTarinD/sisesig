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

    /* Impresión del reporte de comorbilidades en papel carta */
    .print-report-heading { display: none; }

    @page {
        size: letter portrait;
        margin: 12mm;
    }

    @media print {
        html, body {
            width: 8.5in;
            min-height: 11in;
            background: #fff !important;
            color: #212529 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body * {
            visibility: hidden !important;
        }

        #comorbidityReport,
        #comorbidityReport * {
            visibility: visible !important;
        }

        #comorbidityReport {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            border: 1px solid #adb5bd !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        #comorbidityReport .card-body {
            padding: 7mm !important;
        }

        #comorbidityReport .print-report-heading {
            display: block !important;
            text-align: center;
            border-bottom: 1px solid #adb5bd;
            padding-bottom: 4mm;
            margin-bottom: 5mm;
        }

        #comorbidityReport .print-report-heading h1 {
            font-size: 16pt;
            font-weight: 700;
            margin: 0 0 2mm;
        }

        #comorbidityReport .print-report-heading p {
            font-size: 9pt;
            margin: 0;
        }

        #comorbidityReport .no-print {
            display: none !important;
        }

        /* Orden de impresión: gráfica arriba, cuadro de datos y narración debajo */
        #comorbidityReport .row {
            display: flex !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 3mm !important;
        }

        #comorbidityReport .col-12,
        #comorbidityReport .col-lg-6 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }

        /* Gráfica centrada en la parte superior */
        #comorbidityReport .pie-print-container {
            width: 100% !important;
            max-width: 3.55in !important;
            height: 3.05in !important;
            margin: 0 auto !important;
        }

        /* Cuadro de cantidades centrado debajo de la gráfica */
        #comorbidityReport .list-group {
            width: 78% !important;
            max-width: 5.8in !important;
            margin: 2mm auto 0 !important;
        }

        #comorbidityReport .list-group-item {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 3mm 2mm !important;
            font-size: 10pt !important;
            background: #fff !important;
            border-bottom: 1px solid #ced4da !important;
        }

        /* Narración debajo del cuadro, con texto justificado */
        #comorbidityReport .alert {
            width: 90% !important;
            max-width: 6.2in !important;
            margin: 4mm auto 0 !important;
            text-align: center !important;
            background: #f8f9fa !important;
            border: 1px solid #adb5bd !important;
            color: #212529 !important;
            padding: 4mm !important;
        }

        #comorbidityReport .alert .fw-semibold {
            text-align: center !important;
        }

        #comorbidityReport .alert p {
            text-align: justify !important;
            text-justify: inter-word !important;
            margin-top: 2mm !important;
        }

        #comorbidityReport .badge {
            border: 1px solid #adb5bd !important;
            background: #fff !important;
            color: #212529 !important;
            font-size: 9pt !important;
        }


        #comorbidityReport .text-muted,
        #comorbidityReport .small {
            color: #495057 !important;
        }

        #comorbidityReport h5 {
            font-size: 13pt !important;
        }

        #comorbidityReport p {
            font-size: 9.5pt !important;
            line-height: 1.45 !important;
        }
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

        {{-- Filtro de fechas y periodos rápidos --}}
        <div class="d-flex flex-column gap-2">
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-column flex-sm-row align-items-end gap-2">
                <div>
                    <label for="start_date" class="form-label small mb-1">Fecha inicial</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                           value="{{ $startDate->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label for="end_date" class="form-label small mb-1">Fecha final</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                           value="{{ $endDate->format('Y-m-d') }}" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter me-1"></i> Consultar
                </button>
            </form>

            <div class="period-buttons btn-group" role="group" aria-label="Periodos rápidos">
                <a href="{{ route('dashboard', ['period' => 'today']) }}"
                   class="btn btn-sm {{ $period === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">Hoy</a>
                <a href="{{ route('dashboard', ['period' => 'week']) }}"
                   class="btn btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Semana</a>
                <a href="{{ route('dashboard', ['period' => 'month']) }}"
                   class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Mes</a>
                <a href="{{ route('dashboard', ['period' => 'year']) }}"
                   class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-primary' }}">Año</a>
            </div>
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
                            {{ number_format($diabetesOnlyPatients + $bothComorbiditiesPatients) }}
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
                            {{ number_format($hypertensionOnlyPatients + $bothComorbiditiesPatients) }}
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

    {{-- Distribución de comorbilidades de pacientes atendidos en el rango --}}
    <div class="card dashboard-card mb-4" id="comorbidityReport">
        <div class="card-body p-4">
            <div class="print-report-heading">
                <h1>Reporte de atención por comorbilidad</h1>
                <p>Periodo: {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}</p>
                <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Atención por comorbilidad</h5>
                    <p class="text-muted small mb-0">
                        Porcentajes respecto al total de usuarios atendidos del {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}
                    </p>
                    <p class="text-muted small mb-0">
                        Diabetes e hipertensión incluyen a usuarios que presentan ambas condiciones; por ello, sus porcentajes pueden superponerse.
                    </p>
                </div>
                <div class="d-flex align-items-start gap-2 no-print">
                    <span class="badge text-bg-light border align-self-start">
                        Total: {{ number_format($totalAttendedPatients) }} usuarios atendidos
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printComorbidityReport()">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
                    </button>
                </div>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6">
                    @if($totalAttendedPatients > 0)
                        <div class="pie-print-container" style="max-width: 430px; margin: 0 auto; position:relative; height:300px;">
                            <canvas id="comorbidityPieChart" aria-label="Gráfica de pastel con porcentajes de distribución de usuarios atendidos" role="img"></canvas>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-pie-chart fs-1 d-block mb-3"></i>
                            No se registraron usuarios atendidos en el rango seleccionado.
                        </div>
                    @endif
                </div>
                <div class="col-12 col-lg-6">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><strong>Total de usuarios atendidos</strong></span>
                            <strong>{{ number_format($totalAttendedPatients) }} (100%)</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Diabetes (incluye ambas)</span>
                            <strong>{{ number_format($diabetesOnlyPatients + $bothComorbiditiesPatients) }} ({{ number_format(($totalAttendedPatients > 0 ? ($diabetesOnlyPatients + $bothComorbiditiesPatients) * 100 / $totalAttendedPatients : 0), 2) }}%)</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Hipertensión (incluye ambas)</span>
                            <strong>{{ number_format($hypertensionOnlyPatients + $bothComorbiditiesPatients) }} ({{ number_format(($totalAttendedPatients > 0 ? ($hypertensionOnlyPatients + $bothComorbiditiesPatients) * 100 / $totalAttendedPatients : 0), 2) }}%)</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Ambas comorbilidades</span>
                            <strong>{{ number_format($bothComorbiditiesPatients) }} ({{ number_format($overallPercentages['both'], 2) }}%)</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Sin antecedentes registrados de DM/HAS</span>
                            <strong>{{ number_format($noChronicHistoryPatients) }} ({{ number_format($overallPercentages['no_history'], 2) }}%)</strong>
                        </div>
                    </div>
                    <div class="alert alert-light border mt-3 mb-0">
                        <div class="fw-semibold mb-1"><i class="bi bi-card-text me-1"></i> Análisis del periodo</div>
                        <p class="mb-0 small" id="comorbidityAnalysisText">{{ $comorbidityAnalysis }}</p>
                        <div class="text-muted small mt-2 no-print">Análisis descriptivo. La narración generada con IA se incorporará también al reporte impreso.</div>
                        <div class="no-print mt-3">
                            <form id="aiComorbidityAnalysisForm" action="{{ route('dashboard.comorbidity-analysis') }}" method="POST">
                                @csrf
                                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                <button type="submit" class="btn btn-primary btn-sm" id="generateAiAnalysisButton">
                                    <i class="bi bi-stars me-1"></i> Generar análisis con IA
                                </button>
                                <span class="small text-muted ms-2" id="aiComorbidityAnalysisStatus" role="status" aria-live="polite"></span>
                            </form>
                        </div>
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

@push('scripts')
<script>
    function printComorbidityReport() {
        window.print();
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('aiComorbidityAnalysisForm');
        if (!form) return;

        const button = document.getElementById('generateAiAnalysisButton');
        const status = document.getElementById('aiComorbidityAnalysisStatus');
        const analysisText = document.getElementById('comorbidityAnalysisText');
        const originalButtonHtml = button.innerHTML;

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Generando…';
            status.textContent = 'La IA está redactando el análisis. Puede tardar unos segundos.';
            status.className = 'small text-muted ms-2';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(form),
                    credentials: 'same-origin'
                });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Ocurrió un error al generar el análisis.');
                }

                analysisText.textContent = data.analysis;
                status.textContent = 'Análisis generado con IA. Se incluirá al exportar el PDF.';
                status.className = 'small text-success ms-2';
            } catch (error) {
                status.textContent = error.message || 'No fue posible generar el análisis.';
                status.className = 'small text-danger ms-2';
            } finally {
                button.disabled = false;
                button.innerHTML = originalButtonHtml;
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
@if($totalAttendedPatients > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('comorbidityPieChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const comorbidityChart = new Chart(canvas, {
            type: 'pie',
            data: {
                labels: ['Sin antecedentes registrados', 'Diabetes', 'Hipertensión', 'Ambas'],
                datasets: [{
                    data: [
                        {{ $noChronicHistoryPatients }},
                        {{ $diabetesOnlyPatients }},
                        {{ $hypertensionOnlyPatients }},
                        {{ $bothComorbiditiesPatients }}
                    ],
                    backgroundColor: ['#b7d9c8', '#f2b8b5', '#f5df9b', '#b9c9ef'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            plugins: [{
                id: 'percentageLabels',
                afterDatasetsDraw: function (chart) {
                    const ctx = chart.ctx;
                    const dataset = chart.data.datasets[0];
                    const total = {{ $totalAttendedPatients }};
                    chart.getDatasetMeta(0).data.forEach(function (arc, index) {
                        const value = dataset.data[index] || 0;
                        const percentage = total > 0 ? value * 100 / total : 0;
                        if (percentage < 2.5 || value === 0) return;
                        const point = arc.tooltipPosition();
                        ctx.save();
                        ctx.fillStyle = '#344054';
                        ctx.font = 'bold 12px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(percentage.toFixed(2) + '%', point.x, point.y);
                        ctx.restore();
                    });
                }
            }],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels: function (chart) {
                                const dataset = chart.data.datasets[0];
                                const total = {{ $totalAttendedPatients }};
                                return chart.data.labels.map(function (label, index) {
                                    const value = dataset.data[index] || 0;
                                    const percentage = total > 0 ? (value * 100 / total).toFixed(2) : '0.00';
                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: dataset.backgroundColor[index],
                                        strokeStyle: dataset.borderColor,
                                        lineWidth: dataset.borderWidth,
                                        hidden: !chart.getDataVisibility(index),
                                        index: index
                                    };
                                });
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = {{ $totalAttendedPatients }};
                                const value = context.raw || 0;
                                const percentage = total ? (value * 100 / total).toFixed(2) : '0.00';
                                return `${context.label}: ${value} usuarios (${percentage}% del total atendido)`;
                            }
                        }
                    }
                }
            }
        });

        // Chart.js requiere dimensiones explícitas al imprimir; se restauran al cerrar la impresión.
        window.addEventListener('beforeprint', function () {
            comorbidityChart.resize(460, 320);
        });
        window.addEventListener('afterprint', function () {
            comorbidityChart.resize();
        });
    });
</script>
@endif
@endpush
