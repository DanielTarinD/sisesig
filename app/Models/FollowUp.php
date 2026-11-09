<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'patient_id',
        'date',
        'sample_collection_date',
        'blood_pressure',
        'respiratory_rate',
        'pulse',
        'spo2',
        'temperature',
        'without_fasting',
        'capillary_glucose',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'sample_collection_date' => 'date',
            'without_fasting' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
