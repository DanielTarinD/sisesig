<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'date_of_birth',
        'first_visit_date',
        'colonia',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'first_visit_date' => 'date',
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
