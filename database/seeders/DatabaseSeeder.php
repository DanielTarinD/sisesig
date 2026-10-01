<?php

namespace Database\Seeders;

use App\Models\Comorbidity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Diabetes Mellitus (DM)',
            'Hipertensión Arterial Sistémica (HAS)',
            'Otra',
            'No Sabe',
        ] as $name) {
            Comorbidity::firstOrCreate(['name' => $name]);
        }

        User::updateOrCreate(
            ['email' => 'capturista@example.com'],
            ['name' => 'Capturista', 'password' => Hash::make('password'), 'role' => 'capturista']
        );

        User::updateOrCreate(
            ['email' => 'visor@example.com'],
            ['name' => 'Visor', 'password' => Hash::make('password'), 'role' => 'visor']
        );
    }
}
