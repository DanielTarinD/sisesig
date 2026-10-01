<?php

namespace App\Http\Controllers;

use App\Models\Comorbidity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComorbidityController extends Controller
{
    public function index()
    {
        $comorbidities = Comorbidity::withCount('patients')
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();

        return view('comorbidities.index', compact('comorbidities'));
    }

    public function create()
    {
        return view('comorbidities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:comorbidities,name',
            ],
        ], [
            'name.required' => 'El nombre de la comorbilidad es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'name.unique' => 'Esta comorbilidad ya está registrada.',
        ]);

        $data['active'] = true;

        Comorbidity::create($data);

        return redirect()
            ->route('comorbidities.index')
            ->with('success', 'Comorbilidad registrada correctamente.');
    }

    public function edit(Comorbidity $comorbidity)
    {
        $comorbidity->loadCount('patients');

        return view('comorbidities.edit', compact('comorbidity'));
    }

    public function update(Request $request, Comorbidity $comorbidity)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('comorbidities', 'name')
                    ->ignore($comorbidity->id),
            ],
        ], [
            'name.required' => 'El nombre de la comorbilidad es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'name.unique' => 'Esta comorbilidad ya está registrada.',
        ]);

        $comorbidity->update($data);

        return redirect()
            ->route('comorbidities.index')
            ->with('success', 'Comorbilidad actualizada correctamente.');
    }

    public function toggle(Comorbidity $comorbidity)
    {
        $comorbidity->update([
            'active' => ! $comorbidity->active,
        ]);

        $message = $comorbidity->active
            ? 'Comorbilidad activada correctamente.'
            : 'Comorbilidad desactivada correctamente.';

        return redirect()
            ->route('comorbidities.index')
            ->with('success', $message);
    }
}
