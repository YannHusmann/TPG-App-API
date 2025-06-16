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
        Schema::disableForeignKeyConstraints();

        Schema::create('locationUser', function (Blueprint $table) {
            $table->id('loc_id');
            $table->foreignId('loc_use_id')->references('use_id')->on('users')->onDelete('cascade');
            $table->decimal('loc_latitude', 10, 6);
            $table->decimal('loc_longitude', 10, 6);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locationUser');
    }
};
