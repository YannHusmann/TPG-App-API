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

        Schema::create('reports', function (Blueprint $table) {
            $table->id('rep_id');
            $table->foreignId('rep_use_id')->references('use_id')->on('users')->onDelete('cascade');
            $table->string('rep_sto_id', 255)->nullable(); // Changer foreignId en string
            $table->foreign('rep_sto_id')->references('sto_id')->on('stops')->onDelete('set null');
            $table->foreignId('rep_rou_id')->nullable()->references('rou_id')->on('routes')->onDelete('set null');
            $table->longText('rep_message');
            $table->enum('rep_status', ['envoyé', 'en traitement', 'traité'])->default('envoyé');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
