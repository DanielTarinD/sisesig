<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('diabetes_treatment')
                ->nullable()
                ->default(null)
                ->change();

            $table->boolean('hypertension_treatment')
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('diabetes_treatment')
                ->nullable(false)
                ->default(false)
                ->change();

            $table->boolean('hypertension_treatment')
                ->nullable(false)
                ->default(false)
                ->change();
        });
    }
};