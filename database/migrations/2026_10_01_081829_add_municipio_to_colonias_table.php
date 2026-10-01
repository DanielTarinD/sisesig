<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colonias', function (Blueprint $table) {
            $table->string('municipio')
                ->default('ICPEM')
                ->after('id');

            $table->dropUnique('colonias_name_unique');

            $table->unique(
                ['municipio', 'name'],
                'colonias_municipio_name_unique'
            );

            $table->index('municipio');
        });
    }

    public function down(): void
    {
        Schema::table('colonias', function (Blueprint $table) {
            $table->dropUnique('colonias_municipio_name_unique');
            $table->dropIndex(['municipio']);

            $table->unique('name');

            $table->dropColumn('municipio');
        });
    }
};