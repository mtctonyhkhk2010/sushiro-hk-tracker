<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->boolean('local_ticketing_status')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            //
        });
    }
};
