<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn('weekday_cutoff');
            $table->dropColumn('weekend_cutoff');
            $table->dateTime('cutoff');
        });
    }

    public function down(): void
    {
        Schema::table('statistics', function (Blueprint $table) {
            //
        });
    }
};
