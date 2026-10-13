<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Sprawdzamy czy kolumna już istnieje, żeby uniknąć błędów
            if (!Schema::hasColumn('reports', 'data_source_id')) {
                $table->foreignId('data_source_id')->constrained('data_sources')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['data_source_id']);
            $table->dropColumn('data_source_id');
        });
    }
};