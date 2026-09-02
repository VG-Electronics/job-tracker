<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Usuwa istniejące duplikaty przed nałożeniem unikalnego indeksu, żeby migracja się nie wywaliła.
        Artisan::call('offers:remove-duplicates');

        Schema::table('offers', function (Blueprint $table) {
            $table->unique('url');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropUnique(['url']);
        });
    }
};
