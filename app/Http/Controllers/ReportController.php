<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function patient(Patient $patient)
    {
        $patient->load(['comorbidities', 'followUps']);

        return view('reports.patient', compact('patient'));
    }

    public function global(Request $request)
    {
        [$from, $to] = $this->dates($request);

        $followUps = FollowUp::with('patient')
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->orderBy('date', 'desc')
            ->get();

        $totalFollowUps = $followUps->count();

        $totalPatients = $followUps
            ->pluck('patient_id')
            ->unique()
            ->count();

        return view('reports.global', compact(
            'followUps',
            'from',
            'to',
            'totalFollowUps',
            'totalPatients'
        ));
    }

    public function globalCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->dates($request);

        $followUps = FollowUp::with('patient')
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->orderBy('date', 'desc')
            ->get();

        $filename = 'reporte_atenciones_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($followUps) {

            $out = fopen('php://output', 'w');

            // BOM para compatibilidad con Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($out, [
                'Fecha',
                'Hora',
                'Nombre',
            ]);

            foreach ($followUps as $followUp) {

                fputcsv($out, [
                    $followUp->date->format('d/m/Y'),
                    $followUp->date->format('H:i'),
                    $followUp->patient->name,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function globalExcel(Request $request)
    {
        [$from, $to] = $this->dates($request);

        $followUps = FollowUp::with([
            'patient.comorbidities'
        ])
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->orderBy('date', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();

        /*
    |--------------------------------------------------------------------------
    | Hoja 1 - Atenciones
    |--------------------------------------------------------------------------
    */

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Atenciones');

        $headers = [
            'Fecha',
            'Hora',
            'Nombre',
            'Fecha de nacimiento',
            'Edad',
            'Colonia',
            'Comorbilidades',
            'Presión arterial',
            'Frecuencia respiratoria',
            'Pulso',
            'SpO2',
            'Temperatura',
            'Sin ayuno',
            'Glucosa capilar',
        ];

        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);

            $sheet->setCellValue(
                $column . '1',
                $header
            );
        }

        $row = 2;

        foreach ($followUps as $followUp) {

            $patient = $followUp->patient;

            $comorbidities = $patient->comorbidities
                ->pluck('name')
                ->implode(', ');

            $values = [
                $followUp->date->format('d/m/Y'),
                $followUp->date->format('H:i'),
                $patient->name,
                $patient->date_of_birth?->format('d/m/Y'),
                $patient->date_of_birth
                    ? $patient->date_of_birth->age
                    : '',
                $patient->colonia,
                $comorbidities,
                $followUp->blood_pressure,
                $followUp->respiratory_rate,
                $followUp->pulse,
                $followUp->spo2,
                $followUp->temperature,
                $followUp->without_fasting ? 'Sí' : 'No',
                $followUp->capillary_glucose,
            ];

            foreach ($values as $index => $value) {

                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);

                $sheet->setCellValue(
                    $column . $row,
                    $value
                );
            }

            $row++;
        }

        /*
    |--------------------------------------------------------------------------
    | Formato de la hoja Atenciones
    |--------------------------------------------------------------------------
    */

        $lastColumn = $sheet->getHighestColumn();
        $lastRow = max($sheet->getHighestRow(), 1);

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("A1:{$lastColumn}1")
            ->getAlignment()
            ->setHorizontal('center');

        $sheet->freezePane('A2');

        $sheet->setAutoFilter(
            "A1:{$lastColumn}{$lastRow}"
        );

        foreach (
            range(
                1,
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn)
            ) as $index
        ) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);

            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        /*
    |--------------------------------------------------------------------------
    | Hoja 2 - Resumen
    |--------------------------------------------------------------------------
    */

        $summary = $spreadsheet->createSheet();
        $summary->setTitle('Resumen');

        $totalFollowUps = $followUps->count();

        $totalPatients = $followUps
            ->pluck('patient_id')
            ->unique()
            ->count();

        $summary->setCellValue(
            'A1',
            'REPORTE GLOBAL DE ATENCIONES'
        );

        $summary->getStyle('A1')
            ->getFont()
            ->setBold(true);

        $summary->getStyle('A1')
            ->getFont()
            ->setSize(14);

        $summary->setCellValue('A3', 'Desde');

        $summary->setCellValue(
            'B3',
            $from
                ? \Carbon\Carbon::parse($from)->format('d/m/Y')
                : 'Todas'
        );

        $summary->setCellValue('A4', 'Hasta');

        $summary->setCellValue(
            'B4',
            $to
                ? \Carbon\Carbon::parse($to)->format('d/m/Y')
                : 'Todas'
        );

        $summary->setCellValue(
            'A6',
            'Total de atenciones'
        );

        $summary->setCellValue(
            'B6',
            $totalFollowUps
        );

        $summary->setCellValue(
            'A7',
            'Pacientes únicos'
        );

        $summary->setCellValue(
            'B7',
            $totalPatients
        );

        $summary->getStyle('A6:A7')
            ->getFont()
            ->setBold(true);

        $summary->getColumnDimension('A')
            ->setWidth(30);

        $summary->getColumnDimension('B')
            ->setWidth(25);

        /*
    |--------------------------------------------------------------------------
    | Descarga
    |--------------------------------------------------------------------------
    */

        $filename = 'reporte_global_' . now()->format('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    private function dates(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return [
            $data['from'] ?? null,
            $data['to'] ?? null,
        ];
    }
}
