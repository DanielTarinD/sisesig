<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comorbidity extends Model
{
    protected $fillable = [
        'name',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class);
    }
}