
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->enum('gender', [
                'Masculino',
                'Femenino',
                'Otro',
            ])->nullable()->after('date_of_birth');

            $table->boolean('diabetes_treatment')
                ->default(false)
                ->after('gender');

            $table->date('diabetes_treatment_start_date')
                ->nullable()
                ->after('diabetes_treatment');

            $table->boolean('hypertension_treatment')
                ->default(false)
                ->after('diabetes_treatment_start_date');

            $table->date('hypertension_treatment_start_date')
                ->nullable()
                ->after('hypertension_treatment');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'diabetes_treatment',
                'diabetes_treatment_start_date',
                'hypertension_treatment',
                'hypertension_treatment_start_date',
            ]);
        });
    }
};
