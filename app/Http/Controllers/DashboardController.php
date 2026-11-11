<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // El dashboard inicia por defecto mostrando únicamente el día actual.
        $period = $request->input('period', 'today');
        if (! in_array($period, ['today', 'week', 'month', 'year', 'custom'], true)) {
            $period = 'today';
        }

        $today = today();
        $hasCustomRange = $request->filled('start_date') || $request->filled('end_date');

        if ($hasCustomRange) {
            $validated = $request->validate([
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            ], [
                'start_date.required' => 'Selecciona una fecha inicial.',
                'end_date.required' => 'Selecciona una fecha final.',
                'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            ]);

            $startDate = Carbon::parse($validated['start_date'])->startOfDay();
            $endDate = Carbon::parse($validated['end_date'])->startOfDay();
            $period = 'custom';
        } else {
            $startDate = match ($period) {
                'week' => $today->copy()->startOfWeek(),
                'month' => $today->copy()->startOfMonth(),
                'year' => $today->copy()->startOfYear(),
                default => $today->copy()->startOfDay(),
            };
            $endDate = $today->copy()->startOfDay();
        }

        $startDateString = $startDate->toDateString();
        $endDateString = $endDate->toDateString();

        // Indicadores generales
        $totalPatients = Patient::count();
        $totalFollowUps = FollowUp::count();
        $todayFollowUps = FollowUp::whereDate('date', today())->count();

        // Atenciones y pacientes nuevos del periodo seleccionado
        $periodFollowUps = FollowUp::whereBetween(DB::raw('DATE(date)'), [$startDateString, $endDateString])->count();
        $newPatients = Patient::whereBetween(DB::raw('DATE(created_at)'), [$startDateString, $endDateString])->count();

        // Conteo de pacientes únicos que tuvieron al menos una atención en el rango.
        $attendedPatientsQuery = Patient::whereHas('followUps', function ($query) use ($startDateString, $endDateString) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDateString, $endDateString]);
        });

        $diabetesName = 'Diabetes Mellitus (DM)';
        $hypertensionName = 'Hipertensión Arterial Sistémica (HAS)';

        $hasDiabetes = fn ($query) => $query->where('name', $diabetesName);
        $hasHypertension = fn ($query) => $query->where('name', $hypertensionName);

        // Las categorías son mutuamente excluyentes: DM sola, HAS sola y ambas.
        $diabetesOnlyPatients = (clone $attendedPatientsQuery)
            ->whereHas('comorbidities', $hasDiabetes)
            ->whereDoesntHave('comorbidities', $hasHypertension)
            ->count();

        $hypertensionOnlyPatients = (clone $attendedPatientsQuery)
            ->whereHas('comorbidities', $hasHypertension)
            ->whereDoesntHave('comorbidities', $hasDiabetes)
            ->count();

        $bothComorbiditiesPatients = (clone $attendedPatientsQuery)
            ->whereHas('comorbidities', $hasDiabetes)
            ->whereHas('comorbidities', $hasHypertension)
            ->count();

        $totalComorbidityPatients = $diabetesOnlyPatients + $hypertensionOnlyPatients + $bothComorbiditiesPatients;

        // Universo: pacientes únicos con al menos una atención en el rango seleccionado.
        // Los porcentajes se calculan sobre este total, incluyendo usuarios sin antecedentes.
        $totalAttendedPatients = (clone $attendedPatientsQuery)->count();
        $noChronicHistoryPatients = max(
            $totalAttendedPatients - $totalComorbidityPatients,
            0
        );

        $overallPercentages = [
            'no_history' => $totalAttendedPatients > 0
                ? round($noChronicHistoryPatients * 100 / $totalAttendedPatients, 2)
                : 0,
            'diabetes' => $totalAttendedPatients > 0
                ? round($diabetesOnlyPatients * 100 / $totalAttendedPatients, 2)
                : 0,
            'hypertension' => $totalAttendedPatients > 0
                ? round($hypertensionOnlyPatients * 100 / $totalAttendedPatients, 2)
                : 0,
            'both' => $totalAttendedPatients > 0
                ? round($bothComorbiditiesPatients * 100 / $totalAttendedPatients, 2)
                : 0,
        ];

        // Análisis descriptivo con reglas fijas, con el total de usuarios atendidos como denominador.
        if ($totalAttendedPatients === 0) {
            $comorbidityAnalysis = "En el periodo del {$startDate->format('d/m/Y')} al {$endDate->format('d/m/Y')}, no se registraron usuarios atendidos.";
        } else {
            $noHistoryPct = number_format($overallPercentages['no_history'], 2);
            $dmPct = number_format($overallPercentages['diabetes'], 2);
            $hasPct = number_format($overallPercentages['hypertension'], 2);
            $bothPct = number_format($overallPercentages['both'], 2);

            $comorbidityAnalysis = "Se brindó atención a un total de {$totalAttendedPatients} usuarios, del {$startDate->format('d/m/Y')} al {$endDate->format('d/m/Y')}, de los cuales {$noChronicHistoryPatients} ({$noHistoryPct}%) no presentan antecedentes registrados de enfermedades crónico-degenerativas, {$hypertensionOnlyPatients} ({$hasPct}%) presentan hipertensión arterial exclusivamente, {$diabetesOnlyPatients} ({$dmPct}%) presentan diabetes mellitus exclusivamente y {$bothComorbiditiesPatients} ({$bothPct}%) presentan ambas condiciones.";

            $chronicCategories = [
                'hipertensión arterial' => $hypertensionOnlyPatients,
                'diabetes mellitus' => $diabetesOnlyPatients,
                'ambas enfermedades' => $bothComorbiditiesPatients,
            ];
            arsort($chronicCategories);
            $mostFrequentChronic = array_key_first($chronicCategories);
            $mostFrequentChronicCount = reset($chronicCategories);

            if ($overallPercentages['no_history'] > 50) {
                $observation = "En el periodo analizado, la mayoría de los usuarios no cuenta con antecedentes registrados de estas enfermedades; entre quienes sí presentan alguna condición, {$mostFrequentChronic} es la categoría más frecuente ({$mostFrequentChronicCount} usuarios).";
            } elseif ($noChronicHistoryPatients >= max($diabetesOnlyPatients, $hypertensionOnlyPatients, $bothComorbiditiesPatients)) {
                $observation = "En el periodo analizado, el grupo más numeroso corresponde a usuarios sin antecedentes registrados; entre las condiciones crónicas, {$mostFrequentChronic} es la más frecuente ({$mostFrequentChronicCount} usuarios).";
            } else {
                $observation = "Entre las categorías de enfermedades crónico-degenerativas, {$mostFrequentChronic} es la más frecuente ({$mostFrequentChronicCount} usuarios).";
            }

            $comorbidityAnalysis .= " {$observation} Por tal motivo, es importante continuar promoviendo la prevención, la detección oportuna y el seguimiento mediante la toma periódica de signos vitales, orientación sobre alimentación saludable y promoción de hábitos que favorezcan una mejor calidad de vida.";
        }
        // Atenciones por día
        $followUpsByDay = FollowUp::select(DB::raw('DATE(date) as day'), DB::raw('COUNT(*) as total'))
            ->whereBetween(DB::raw('DATE(date)'), [$startDateString, $endDateString])
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('day')
            ->get();

        // Promedio general de atenciones por paciente
        $averageFollowUps = $totalPatients > 0 ? round($totalFollowUps / $totalPatients, 1) : 0;

        $periodLabel = match ($period) {
            'today' => 'Hoy',
            'week' => 'Esta semana',
            'month' => 'Este mes',
            'year' => 'Este año',
            default => 'Rango personalizado',
        };

        return view('dashboard', compact(
            'period', 'periodLabel', 'startDate', 'endDate', 'totalPatients',
            'totalFollowUps', 'todayFollowUps', 'periodFollowUps', 'newPatients',
            'diabetesOnlyPatients', 'hypertensionOnlyPatients', 'bothComorbiditiesPatients',
            'totalComorbidityPatients', 'totalAttendedPatients', 'noChronicHistoryPatients', 'overallPercentages', 'comorbidityAnalysis', 'averageFollowUps', 'followUpsByDay'
        ));
    }

    /**
     * Genera una narración descriptiva con IA usando exclusivamente datos agregados.
     * No envía nombres, identificadores ni información individual de pacientes.
     */
    public function generateComorbidityAnalysis(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'start_date.required' => 'Selecciona una fecha inicial.',
            'end_date.required' => 'Selecciona una fecha final.',
            'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
        ]);

        $apiKey = config('openai.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'message' => 'La API Key de OpenAI aún no está configurada. Agrega OPENAI_API_KEY al archivo .env y limpia la caché de configuración.',
            ], 503);
        }

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();
        $startDateString = $startDate->toDateString();
        $endDateString = $endDate->toDateString();

        $attendedPatientsQuery = Patient::whereHas('followUps', function ($query) use ($startDateString, $endDateString) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDateString, $endDateString]);
        });

        $diabetesName = 'Diabetes Mellitus (DM)';
        $hypertensionName = 'Hipertensión Arterial Sistémica (HAS)';
        $hasDiabetes = fn ($query) => $query->where('name', $diabetesName);
        $hasHypertension = fn ($query) => $query->where('name', $hypertensionName);

        $diabetesOnly = (clone $attendedPatientsQuery)
            ->whereHas('comorbidities', $hasDiabetes)
            ->whereDoesntHave('comorbidities', $hasHypertension)
            ->count();

        $hypertensionOnly = (clone $attendedPatientsQuery)
            ->whereHas('comorbidities', $hasHypertension)
            ->whereDoesntHave('comorbidities', $hasDiabetes)
            ->count();

        $both = (clone $attendedPatientsQuery)
            ->whereHas('comorbidities', $hasDiabetes)
            ->whereHas('comorbidities', $hasHypertension)
            ->count();

        // Denominador de IA: todos los pacientes únicos atendidos en el periodo.
        $totalAttended = (clone $attendedPatientsQuery)->count();
        $totalWithChronicHistory = $diabetesOnly + $hypertensionOnly + $both;
        $noChronicHistory = max($totalAttended - $totalWithChronicHistory, 0);

        $percentages = [
            'no_history' => $totalAttended > 0 ? round($noChronicHistory * 100 / $totalAttended, 2) : 0,
            'diabetes' => $totalAttended > 0 ? round($diabetesOnly * 100 / $totalAttended, 2) : 0,
            'hypertension' => $totalAttended > 0 ? round($hypertensionOnly * 100 / $totalAttended, 2) : 0,
            'both' => $totalAttended > 0 ? round($both * 100 / $totalAttended, 2) : 0,
        ];

        // Se envían únicamente datos estadísticos agregados, sin información identificable.
        $input = json_encode([
            'periodo' => $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y'),
            'total_usuarios_atendidos' => $totalAttended,
            'sin_antecedentes_DM_HAS' => ['n' => $noChronicHistory, 'pct' => $percentages['no_history']],
            'diabetes_exclusiva' => ['n' => $diabetesOnly, 'pct' => $percentages['diabetes']],
            'hipertension_exclusiva' => ['n' => $hypertensionOnly, 'pct' => $percentages['hypertension']],
            'ambas' => ['n' => $both, 'pct' => $percentages['both']],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            $apiResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(45)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('openai.model', 'gpt-6-luna'),
                    'instructions' => <<<'PROMPT'
Redacta en español un análisis institucional de salud pública siguiendo este estilo: descripción del total de usuarios y porcentajes, observación de la distribución y recomendación general de prevención, detección y seguimiento.

Usa SIEMPRE como denominador el total de usuarios atendidos del periodo. Menciona cantidades y porcentajes de: usuarios sin antecedentes registrados, diabetes exclusiva, hipertensión exclusiva y ambas. Describe cuál categoría crónica es más frecuente; solo afirma que hay mayoría sin antecedentes si ese porcentaje supera 50%.

Recomienda continuar con prevención, detección oportuna y seguimiento mediante toma periódica de signos vitales, orientación sobre alimentación saludable y promoción de hábitos saludables. No recomiendes tratamientos ni infieras prevalencia poblacional, causalidad, tendencias o control clínico. Usa solo los datos proporcionados.

Máximo 110 palabras, en 2 párrafos, sin encabezados ni viñetas. Si total_usuarios_atendidos=0, indica únicamente que no hubo usuarios atendidos en el periodo.
PROMPT,
                    'input' => $input,
                    'max_output_tokens' => 300,
                ]);

            if ($apiResponse->failed()) {
                \Illuminate\Support\Facades\Log::warning('OpenAI API respondió con error al generar análisis de comorbilidades.', [
                    'status' => $apiResponse->status(),
                ]);

                return response()->json([
                    'message' => 'No fue posible generar el análisis con IA. Verifica que la API Key y la facturación de OpenAI estén activas.',
                ], 502);
            }

            $responseData = $apiResponse->json();
            $analysis = collect($responseData['output'] ?? [])
                ->filter(fn ($item) => ($item['type'] ?? null) === 'message')
                ->flatMap(fn ($item) => $item['content'] ?? [])
                ->filter(fn ($content) => ($content['type'] ?? null) === 'output_text')
                ->pluck('text')
                ->filter()
                ->implode("\n");

            if (trim($analysis) === '') {
                return response()->json([
                    'message' => 'La IA no devolvió una narración utilizable. Intenta nuevamente.',
                ], 502);
            }

            return response()->json([
                'analysis' => trim($analysis),
                'model' => config('openai.model', 'gpt-6-luna'),
            ]);
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('Error de conexión al servicio de OpenAI para análisis de comorbilidades.', [
                'exception' => get_class($exception),
            ]);

            return response()->json([
                'message' => 'No se pudo conectar con OpenAI. Verifica la conectividad del servidor e inténtalo nuevamente.',
            ], 502);
        }
    }

}
