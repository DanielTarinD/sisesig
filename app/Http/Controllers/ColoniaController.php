<?php

namespace App\Http\Controllers;

use App\Models\Colonia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColoniaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $colonias = Colonia::query()
            ->when($search !== '', function ($query) use ($search) {
                $terms = preg_split(
                    '/\s+/',
                    $search,
                    -1,
                    PREG_SPLIT_NO_EMPTY
                );

                foreach ($terms as $term) {
                    $term = \Illuminate\Support\Str::ascii($term);

                    $query->where(function ($query) use ($term) {
                        $query
                            ->whereRaw(
                                "municipio COLLATE utf8mb4_unicode_ci LIKE ?",
                                ['%' . $term . '%']
                            )
                            ->orWhereRaw(
                                "name COLLATE utf8mb4_unicode_ci LIKE ?",
                                ['%' . $term . '%']
                            );
                    });
                }
            })
            ->orderBy('municipio')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('colonias.index', compact(
            'colonias',
            'search'
        ));
    }

    public function create()
    {
        return view('colonias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'municipio' => [
                'required',
                'string',
                'max:255',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'municipio.required' =>
            'El municipio es obligatorio.',

            'name.required' =>
            'El nombre de la colonia es obligatorio.',
        ]);

        $data['municipio'] = trim($data['municipio']);
        $data['name'] = trim($data['name']);
        $data['active'] = true;

        $exists = Colonia::where(
            'municipio',
            $data['municipio']
        )
            ->where(
                'name',
                $data['name']
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                    'Esta colonia ya está registrada en ese municipio.',
                ]);
        }

        Colonia::create($data);

        return redirect()
            ->route('colonias.index')
            ->with(
                'success',
                'Colonia registrada correctamente.'
            );
    }

    public function edit(Colonia $colonia)
    {
        return view('colonias.edit', compact('colonia'));
    }

    public function update(
        Request $request,
        Colonia $colonia
    ) {
        $data = $request->validate([
            'municipio' => [
                'required',
                'string',
                'max:255',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'municipio.required' =>
            'El municipio es obligatorio.',

            'name.required' =>
            'El nombre de la colonia es obligatorio.',
        ]);

        $data['municipio'] = trim($data['municipio']);
        $data['name'] = trim($data['name']);

        $exists = Colonia::where(
            'municipio',
            $data['municipio']
        )
            ->where(
                'name',
                $data['name']
            )
            ->where(
                'id',
                '!=',
                $colonia->id
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                    'Esta colonia ya está registrada en ese municipio.',
                ]);
        }

        $colonia->update($data);

        return redirect()
            ->route('colonias.index')
            ->with(
                'success',
                'Colonia actualizada correctamente.'
            );
    }

    public function toggle(Colonia $colonia)
    {
        $colonia->update([
            'active' => ! $colonia->active,
        ]);

        $message = $colonia->active
            ? 'Colonia activada correctamente.'
            : 'Colonia desactivada correctamente.';

        return redirect()
            ->route('colonias.index')
            ->with('success', $message);
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'municipio' => [
                'required',
                'string',
                'max:255',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'municipio.required' => 'Debe seleccionar un municipio.',

            'name.required' =>
            'El nombre de la colonia es obligatorio.',

            'name.max' =>
            'El nombre de la colonia no puede exceder 255 caracteres.',
        ]);

        $data['municipio'] = trim($data['municipio']);
        $data['name'] = trim($data['name']);
        $data['active'] = true;


        /*
     * Buscar si ya existe en ese municipio.
     */

        $colonia = Colonia::where('municipio', $data['municipio'])
            ->where('name', $data['name'])
            ->first();


        /*
     * Si existe pero estaba inactiva,
     * la reactivamos.
     */

        if ($colonia) {

            if (! $colonia->active) {

                $colonia->update([
                    'active' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'id' => $colonia->id,
                'name' => $colonia->name,
                'municipio' => $colonia->municipio,
                'display' =>
                $colonia->municipio . ' - ' . $colonia->name,
            ]);
        }


        /*
     * Crear nueva colonia.
     */

        $colonia = Colonia::create($data);


        return response()->json([
            'success' => true,
            'id' => $colonia->id,
            'name' => $colonia->name,
            'municipio' => $colonia->municipio,
            'display' =>
            $colonia->municipio . ' - ' . $colonia->name,
        ]);
    }
}
