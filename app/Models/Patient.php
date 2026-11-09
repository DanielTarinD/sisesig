<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'gender',
        'diabetes_treatment',
        'diabetes_treatment_start_date',
        'hypertension_treatment',
        'hypertension_treatment_start_date',
        'date_of_birth',
        'first_visit_date',
        'sample_collection_date',
        'colonia',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'first_visit_date' => 'date',
            'diabetes_treatment' => 'boolean',
            'diabetes_treatment_start_date' => 'date',
            'hypertension_treatment' => 'boolean',
            'hypertension_treatment_start_date' => 'date',
        ];
    }

    public function comorbidities()
    {
        return $this->belongsToMany(Comorbidity::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class)->latest('date');
    }
}
