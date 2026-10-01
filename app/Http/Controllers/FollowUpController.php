<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'blood_pressure' => [
                'required',
                'string',
                'regex:/^\d{2,3}\/\d{2,3}$/',
                'max:7',
            ],

            'respiratory_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:300',
            ],

            'pulse' => [
                'required',
                'numeric',
                'min:0',
                'max:300',
            ],

            'spo2' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'temperature' => [
                'required',
                'numeric',
                'min:0',
                'max:60',
            ],

            'without_fasting' => [
                'boolean',
            ],

            'capillary_glucose' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000',
            ],
        ], [
            'blood_pressure.required' => 'La tensión arterial es obligatoria.',
            'blood_pressure.regex' => 'La tensión arterial debe tener el formato 120/80.',

            'respiratory_rate.required' => 'La frecuencia respiratoria es obligatoria.',
            'respiratory_rate.numeric' => 'La frecuencia respiratoria debe ser numérica.',
            'respiratory_rate.min' => 'La frecuencia respiratoria no puede ser negativa.',
            'respiratory_rate.max' => 'La frecuencia respiratoria no es válida.',

            'pulse.required' => 'El pulso es obligatorio.',
            'pulse.numeric' => 'El pulso debe ser numérico.',
            'pulse.min' => 'El pulso no puede ser negativo.',
            'pulse.max' => 'El pulso no es válido.',

            'spo2.required' => 'La SpO2 es obligatoria.',
            'spo2.numeric' => 'La SpO2 debe ser numérica.',
            'spo2.min' => 'La SpO2 no puede ser negativa.',
            'spo2.max' => 'La SpO2 no puede ser mayor a 100.',

            'temperature.required' => 'La temperatura es obligatoria.',
            'temperature.numeric' => 'La temperatura debe ser numérica.',
            'temperature.min' => 'La temperatura no puede ser negativa.',
            'temperature.max' => 'La temperatura no es válida.',

            'capillary_glucose.numeric' => 'La glucemia capilar debe ser numérica.',
            'capillary_glucose.min' => 'La glucemia capilar no puede ser negativa.',
            'capillary_glucose.max' => 'La glucemia capilar no es válida.',
        ]);

        $data['date'] = now();

        $data['without_fasting'] = $request->boolean('without_fasting');

        $patient->followUps()->create($data);

        return back()->with(
            'success',
            'Seguimiento registrado correctamente.'
        );
    }

    public function destroy(FollowUp $followUp)
    {
        $followUp->delete();
        return back()->with('success', 'Seguimiento eliminado.');
    }
}
