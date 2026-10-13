<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Właściciel przestrzeni
            $table->timestamps();
        });

        // Dodajemy workspace_id do tabeli data_sources (pliki)
        Schema::table('data_sources', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Dodajemy workspace_id do tabeli reports
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
        });

        // Dodajemy workspace_id do tabeli dashboards
        Schema::table('dashboards', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
