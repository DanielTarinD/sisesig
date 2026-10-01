<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'patient_id',
        'date',
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
            'without_fasting' => 'boolean',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
