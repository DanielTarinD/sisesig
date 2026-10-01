<?php

namespace App\Http\Controllers;

use App\Models\Comorbidity;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use App\Models\Colonia;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $colonia = trim((string) $request->get('colonia'));
        $dateOfBirth = trim((string) $request->get('date_of_birth'));

        $patients = Patient::query()
            ->with([
                'comorbidities',
                'followUps:id,patient_id,date',
            ])

            /*
        |--------------------------------------------------------------------------
        | Buscar por nombre
        |--------------------------------------------------------------------------
        */

            ->when($q !== '', function ($query) use ($q) {

                $terms = preg_split(
                    '/\s+/',
                    trim($q),
                    -1,
                    PREG_SPLIT_NO_EMPTY
                );

                foreach ($terms as $term) {

                    $term = \Illuminate\Support\Str::ascii($term);

                    $query->whereRaw(
                        "name COLLATE utf8mb4_unicode_ci LIKE ?",
                        ['%' . $term . '%']
                    );
                }
            })

            /*
        |--------------------------------------------------------------------------
        | Filtrar por colonia
        |--------------------------------------------------------------------------
        */

            ->when($colonia !== '', function ($query) use ($colonia) {

                $query->where(
                    'colonia',
                    $colonia
                );
            })

            /*
        |--------------------------------------------------------------------------
        | Filtrar por fecha de nacimiento
        |--------------------------------------------------------------------------
        */

            ->when($dateOfBirth !== '', function ($query) use ($dateOfBirth) {

                $query->whereDate(
                    'date_of_birth',
                    $dateOfBirth
                );
            })

            ->orderBy('name')

            ->paginate(15)

            ->withQueryString();


        /*
    |--------------------------------------------------------------------------
    | Colonias para el filtro
    |--------------------------------------------------------------------------
    */

        $colonias = \App\Models\Colonia::query()
            ->orderBy('name')
            ->get();


        return view(
            'patients.index',
            compact(
                'patients',
                'q',
                'colonia',
                'dateOfBirth',
                'colonias'
            )
        );
    }

    public function create()
    {
        $comorbidities = Comorbidity::where('active', true)
            ->orderBy('name')
            ->get();

        $colonias = \App\Models\Colonia::where('active', true)
            ->orderBy('municipio')
            ->orderBy('name')
            ->get();

        $municipios = \App\Models\Colonia::where('active', true)
            ->whereNotNull('municipio')
            ->where('municipio', '!=', '')
            ->select('municipio')
            ->distinct()
            ->orderBy('municipio')
            ->pluck('municipio');

        return view(
            'patients.create',
            compact('comorbidities', 'colonias', 'municipios')
        );
    }

    public function store(Request $request)
    {

        $data = $this->validatePatient($request);

        $duplicates = $this->findPotentialDuplicates(
            $data['name'],
            $data['date_of_birth']
        );

        if ($duplicates->isNotEmpty() && ! $request->boolean('confirm_duplicate')) {
            return back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'Existe un paciente con datos que pueden corresponder a este registro. Revise el expediente antes de continuar.',
                ]);
        }

        $data['first_visit_date'] = $request->boolean('first_visit')
            ? ($data['first_visit_date'] ?? now()->toDateString())
            : null;

        unset($data['first_visit']);

        $patient = Patient::create($data);
        $patient->comorbidities()->sync($request->input('comorbidities', []));

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['comorbidities', 'followUps']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $patient->load('comorbidities');

        /*
    |--------------------------------------------------------------------------
    | Comorbilidades disponibles
    |--------------------------------------------------------------------------
    | Se muestran:
    | - Todas las activas.
    | - Las inactivas que actualmente pertenecen al paciente.
    |--------------------------------------------------------------------------
    */

        $comorbidities = Comorbidity::where('active', true)
            ->orWhereIn(
                'id',
                $patient->comorbidities
                    ->where('active', false)
                    ->pluck('id')
            )
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Colonias disponibles
    |--------------------------------------------------------------------------
    */

        $colonias = \App\Models\Colonia::where('active', true)
            ->when(
                $patient->colonia,
                function ($query) use ($patient) {
                    $query->orWhere(function ($query) use ($patient) {
                        $query
                            ->where('active', false)
                            ->where('name', $patient->colonia);
                    });
                }
            )
            ->orderBy('municipio')
            ->orderBy('name')
            ->get();

        $municipios = \App\Models\Colonia::where('active', true)
            ->whereNotNull('municipio')
            ->where('municipio', '!=', '')
            ->select('municipio')
            ->distinct()
            ->orderBy('municipio')
            ->pluck('municipio');

        return view(
            'patients.edit',
            compact('patient', 'comorbidities', 'colonias', 'municipios')
        );
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $this->validatePatient($request, $patient);

        $data['first_visit_date'] = $request->boolean('first_visit')
            ? (
                $data['first_visit_date']
                ?? $patient->first_visit_date?->toDateString()
                ?? now()->toDateString()
            )
            : null;

        unset($data['first_visit']);

        $patient->update($data);

        $patient->comorbidities()->sync(
            $request->input('comorbidities', [])
        );

        return redirect()
            ->route('patients.show', $patient)
            ->with(
                'success',
                'Expediente actualizado correctamente.'
            );
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q'));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $terms = preg_split(
            '/\s+/',
            $q,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $patients = Patient::query();

        foreach ($terms as $term) {

            $term = \Illuminate\Support\Str::ascii($term);

            $patients->whereRaw(
                "name COLLATE utf8mb4_unicode_ci LIKE ?",
                ['%' . $term . '%']
            );
        }

        return $patients
            ->orderBy('name')
            ->limit(10)
            ->get([
                'id',
                'name',
                'date_of_birth',
            ])
            ->map(fn($patient) => [
                'id' => $patient->id,
                'name' => $patient->name,
                'date_of_birth' => $patient->date_of_birth
                    ?->format('d/m/Y'),
            ]);
    }

    public function checkDuplicate(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'patient_id' => [
                'nullable',
                'integer',
                'exists:patients,id',
            ],
        ]);

        $normalizedName =
            $this->normalizePatientName(
                $data['name']
            );


        /*
    |--------------------------------------------------------------------------
    | Buscar candidatos por fecha de nacimiento
    |--------------------------------------------------------------------------
    */

        $query = Patient::query()
            ->whereDate(
                'date_of_birth',
                $data['date_of_birth']
            )
            ->orderBy('name');


        /*
    |--------------------------------------------------------------------------
    | En edición excluir al paciente actual
    |--------------------------------------------------------------------------
    */

        if (!empty($data['patient_id'])) {

            $query->where(
                'id',
                '!=',
                $data['patient_id']
            );
        }


        $patients = $query->get([
            'id',
            'name',
            'date_of_birth',
            'colonia',
        ]);


        /*
    |--------------------------------------------------------------------------
    | Comparar nombres normalizados
    |--------------------------------------------------------------------------
    */

        $duplicates = $patients->filter(function ($patient) use ($normalizedName) {

            return $this->normalizePatientName(
                $patient->name
            ) === $normalizedName;
        })->values();


        /*
    |--------------------------------------------------------------------------
    | Información para la interfaz
    |--------------------------------------------------------------------------
    */

        return response()->json(

            $duplicates->map(function ($patient) {

                $lastAttention = $patient->followUps()
                    ->latest('date')
                    ->value('date');

                return [

                    'id' =>
                    $patient->id,

                    'name' =>
                    $patient->name,

                    'date_of_birth' =>
                    $patient->date_of_birth?->format('d/m/Y'),

                    'age' =>
                    $patient->date_of_birth?->age,

                    'colonia' =>
                    $patient->colonia ?: '—',

                    'last_attention' =>
                    $lastAttention
                        ? \Carbon\Carbon::parse(
                            $lastAttention
                        )->format('d/m/Y H:i')
                        : 'Sin atenciones',

                ];
            })

        );
    }

    private function findPotentialDuplicates(
        string $name,
        string $dateOfBirth,
        ?int $excludePatientId = null
    ) {
        $normalizedName = $this->normalizePatientName($name);

        $query = Patient::query()
            ->whereDate('date_of_birth', $dateOfBirth);

        if ($excludePatientId !== null) {
            $query->where('id', '!=', $excludePatientId);
        }

        return $query
            ->orderBy('id')
            ->get()
            ->filter(function ($patient) use ($normalizedName) {

                return $this->normalizePatientName(
                    $patient->name
                ) === $normalizedName;
            })
            ->values();
    }

    private function normalizePatientName(string $name): string
    {
        /*
    Convertir caracteres especiales y acentos.
    Ejemplo:
    José Pérez -> Jose Perez
    */

        $name = Str::ascii($name);

        /*
    Convertir a minúsculas.
    */

        $name = mb_strtolower($name, 'UTF-8');

        /*
    Sustituir cualquier grupo de caracteres
    que no sean letras o números por un espacio.
    */

        $name = preg_replace('/[^a-z0-9]+/i', ' ', $name);

        /*
    Eliminar espacios duplicados y extremos.
    */

        $name = preg_replace('/\s+/', ' ', $name);

        return trim($name);
    }

    private function validatePatient(
        Request $request,
        ?Patient $patient = null
    ): array {

        $comorbidityRule = \Illuminate\Validation\Rule::exists(
            'comorbidities',
            'id'
        )->where(function ($query) use ($patient) {

            /*
        |--------------------------------------------------------------------------
        | Siempre permitir comorbilidades activas
        |--------------------------------------------------------------------------
        */

            $query->where('active', true);


            /*
        |--------------------------------------------------------------------------
        | En edición:
        | permitir también una comorbilidad inactiva si ya pertenece
        | actualmente al paciente.
        |--------------------------------------------------------------------------
        */

            if ($patient) {

                $query->orWhere(function ($query) use ($patient) {

                    $query
                        ->where('active', false)
                        ->whereIn(
                            'id',
                            $patient->comorbidities()
                                ->pluck('comorbidities.id')
                        );
                });
            }
        });


        return $request->validate(
            [

                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'date_of_birth' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],

                'first_visit' => [
                    'nullable',
                    'boolean',
                ],

                'first_visit_date' => [
                    'nullable',
                    'date',
                    'before_or_equal:today',
                    'after_or_equal:date_of_birth',
                ],

                'colonia' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'comorbidities' => [
                    'nullable',
                    'array',
                ],

                'comorbidities.*' => [
                    'integer',
                    $comorbidityRule,
                ],

            ],
            [

                'name.required' =>
                'El nombre del paciente es obligatorio.',

                'date_of_birth.required' =>
                'La fecha de nacimiento es obligatoria.',

                'date_of_birth.before_or_equal' =>
                'La fecha de nacimiento no puede ser posterior a hoy.',

                'first_visit_date.before_or_equal' =>
                'La fecha de primera visita no puede ser posterior a hoy.',

                'first_visit_date.after_or_equal' =>
                'La fecha de primera visita no puede ser anterior a la fecha de nacimiento.',

            ]
        );
    }
}
