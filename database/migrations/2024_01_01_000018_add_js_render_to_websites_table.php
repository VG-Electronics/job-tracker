<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->boolean('js_render')->default(false)->after('offer_url_part');
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('js_render');
        });
    }
};
