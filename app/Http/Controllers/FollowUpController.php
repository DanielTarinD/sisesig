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
            'date' => ['required', 'date', 'before_or_equal:today'],
            'blood_pressure' => ['required', 'string', 'max:50'],
            'respiratory_rate' => ['required', 'string', 'max:50'],
            'pulse' => ['required', 'string', 'max:50'],
            'spo2' => ['required', 'string', 'max:50'],
            'temperature' => ['required', 'string', 'max:50'],
            'without_fasting' => ['boolean'],
            'capillary_glucose' => ['nullable', 'string', 'max:50'],
        ]);

        // La fecha seleccionada se conserva y la hora se registra automáticamente.
        $data['date'] = \Carbon\Carbon::parse($data['date'])
            ->setTimeFrom(now());

        $data['without_fasting'] = $request->boolean('without_fasting');

        $patient->followUps()->create($data);

        return back()->with('success', 'Seguimiento registrado correctamente.');
    }
    public function edit(FollowUp $followUp)
    {
        $followUp->load('patient');

        return view('followups.edit', compact('followUp'));
    }

    public function update(Request $request, FollowUp $followUp)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'blood_pressure' => ['required', 'string', 'max:50'],
            'respiratory_rate' => ['required', 'string', 'max:50'],
            'pulse' => ['required', 'string', 'max:50'],
            'spo2' => ['required', 'string', 'max:50'],
            'temperature' => ['required', 'string', 'max:50'],
            'without_fasting' => ['nullable', 'boolean'],
            'capillary_glucose' => ['nullable', 'string', 'max:50'],
        ]);

        $data['date'] = \Carbon\Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['date'] . ' ' . $data['time']
        );

        unset($data['time']);
        $data['without_fasting'] = $request->boolean('without_fasting');

        $followUp->update($data);

        return redirect()
            ->route('patients.show', $followUp->patient_id)
            ->with('success', 'Atención modificada correctamente.');
    }

    public function destroy(FollowUp $followUp)
    {
        $followUp->delete();
        return back()->with('success', 'Seguimiento eliminado.');
    }
}
