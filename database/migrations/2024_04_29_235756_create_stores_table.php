<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('sushiro_store_id');
            $table->json('name');
            $table->string('status');
            $table->string('address');
            $table->string('region');
            $table->geometry('location', subtype: 'point');
            $table->json('store_queue')->nullable();
            $table->integer('wait_group')->nullable();//waitingGroup
            $table->integer('wait_time')->nullable();//wait
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
