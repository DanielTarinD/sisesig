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

        // Redacción descriptiva basada en reglas, no utiliza API ni transmite información.
        if ($totalComorbidityPatients === 0) {
            $comorbidityAnalysis = 'En el periodo seleccionado no se identificaron pacientes atendidos con diabetes, hipertensión o ambas comorbilidades.';
        } else {
            $dmPercentage = round(($diabetesOnlyPatients / $totalComorbidityPatients) * 100, 1);
            $hasPercentage = round(($hypertensionOnlyPatients / $totalComorbidityPatients) * 100, 1);
            $bothPercentage = round(($bothComorbiditiesPatients / $totalComorbidityPatients) * 100, 1);

            $comorbidityAnalysis = "En el periodo del {$startDate->format('d/m/Y')} al {$endDate->format('d/m/Y')}, se registraron {$totalComorbidityPatients} pacientes únicos atendidos con las comorbilidades analizadas. {$diabetesOnlyPatients} ({$dmPercentage} %) presentaron diabetes exclusivamente, {$hypertensionOnlyPatients} ({$hasPercentage} %) hipertensión exclusivamente y {$bothComorbiditiesPatients} ({$bothPercentage} %) ambas condiciones. Estas cifras describen la distribución de pacientes atendidos en el periodo seleccionado.";
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
            'totalComorbidityPatients', 'comorbidityAnalysis', 'averageFollowUps', 'followUpsByDay'
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

        $total = $diabetesOnly + $hypertensionOnly + $both;
        $percentages = [
            'diabetes' => $total > 0 ? round($diabetesOnly * 100 / $total, 1) : 0,
            'hypertension' => $total > 0 ? round($hypertensionOnly * 100 / $total, 1) : 0,
            'both' => $total > 0 ? round($both * 100 / $total, 1) : 0,
        ];

        // Datos agregados que se envían a la IA; nunca se incluyen datos identificables.
        $input = json_encode([
            'periodo' => $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y'),
            'total' => $total,
            'DM_exclusiva' => ['n' => $diabetesOnly, 'pct' => $percentages['diabetes']],
            'HAS_exclusiva' => ['n' => $hypertensionOnly, 'pct' => $percentages['hypertension']],
            'DM_HAS' => ['n' => $both, 'pct' => $percentages['both']],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            $apiResponse = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(45)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('openai.model', 'gpt-6-luna'),
                    'instructions' => <<<'PROMPT'
Analiza estos datos agregados de pacientes en salud pública. Responde en español institucional, conciso y sin repetir cifras innecesariamente. Usa solo los datos recibidos; no infieras prevalencia poblacional, tendencias, causas ni control clínico. No recomiendes tratamientos.

Máximo 120 palabras. Formato:
ANÁLISIS GENERAL: periodo, total y categoría mayoritaria.
HALLAZGOS: 2 viñetas breves con cantidades y porcentajes.
INTERPRETACIÓN: 1 oración cautelosa.
RECOMENDACIONES: 3 acciones operativas breves y pertinentes.
CONCLUSIÓN: 1 oración.

Si total=0, informa que no hay pacientes contabilizados y omite recomendaciones. Devuelve únicamente estos apartados.
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
