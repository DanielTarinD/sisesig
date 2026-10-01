<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'month');

        if (! in_array($period, ['today', 'week', 'month', 'year'], true)) {
            $period = 'month';
        }

        $startDate = match ($period) {
            'today' => today(),
            'week'  => today()->startOfWeek(),
            'year'  => today()->startOfYear(),
            default => today()->startOfMonth(),
        };

        $endDate = today();

        /*
        |--------------------------------------------------------------------------
        | Indicadores generales
        |--------------------------------------------------------------------------
        */

        $totalPatients = Patient::count();

        $totalFollowUps = FollowUp::count();

        $todayFollowUps = FollowUp::whereDate('date', today())->count();

        /*
        |--------------------------------------------------------------------------
        | Indicadores del periodo seleccionado
        |--------------------------------------------------------------------------
        */

        $periodFollowUps = FollowUp::whereBetween(
            DB::raw('DATE(date)'),
            [$startDate->toDateString(), $endDate->toDateString()]
        )->count();

        $newPatients = Patient::whereBetween(
            DB::raw('DATE(created_at)'),
            [$startDate->toDateString(), $endDate->toDateString()]
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Comorbilidades
        |--------------------------------------------------------------------------
        */

        $diabetesPatients = Patient::whereHas('comorbidities', function ($query) {
            $query->where('name', 'Diabetes Mellitus (DM)');
        })->count();

        $hypertensionPatients = Patient::whereHas('comorbidities', function ($query) {
            $query->where('name', 'Hipertensión Arterial Sistémica (HAS)');
        })->count();

        /*
        |--------------------------------------------------------------------------
        | Atenciones por día
        |--------------------------------------------------------------------------
        */

        $followUpsByDay = FollowUp::select(
                DB::raw('DATE(date) as day'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween(
                DB::raw('DATE(date)'),
                [$startDate->toDateString(), $endDate->toDateString()]
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('day')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Promedio de atenciones por paciente
        |--------------------------------------------------------------------------
        */

        $averageFollowUps = $totalPatients > 0
            ? round($totalFollowUps / $totalPatients, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Periodo mostrado
        |--------------------------------------------------------------------------
        */

        $periodLabel = match ($period) {
            'today' => 'Hoy',
            'week'  => 'Esta semana',
            'year'  => 'Este año',
            default => 'Este mes',
        };

        return view('dashboard', compact(
            'period',
            'periodLabel',
            'startDate',
            'endDate',
            'totalPatients',
            'totalFollowUps',
            'todayFollowUps',
            'periodFollowUps',
            'newPatients',
            'diabetesPatients',
            'hypertensionPatients',
            'averageFollowUps',
            'followUpsByDay'
        ));
    }
}