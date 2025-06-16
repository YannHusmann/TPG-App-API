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
        Schema::create('stops', function (Blueprint $table) {
            $table->string('sto_id', 255)->primary();
            $table->string('sto_name', 255)->nullable();
            $table->string('sto_municipality', 255)->nullable();
            $table->string('sto_country', 255)->nullable();
            $table->decimal('sto_latitude', 10, 6)->nullable();
            $table->decimal('sto_longitude', 10, 6)->nullable();
            $table->enum('sto_actif', ['Y', 'N'])->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stops');
    }
};
