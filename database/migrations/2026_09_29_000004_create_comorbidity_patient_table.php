<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comorbidity_patient', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('comorbidity_id')->constrained()->cascadeOnDelete();
            $table->primary(['patient_id', 'comorbidity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comorbidity_patient');
    }
};
