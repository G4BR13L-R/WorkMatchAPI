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
        Schema::create('experiencias_profissionais', function (Blueprint $table) {
            $table->id();
            $table->string('cargo', 255);
            $table->string('empresa', 255);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('contratado_id');
            $table->foreign('contratado_id')->references('id')->on('contratados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiencias_profissionais');
    }
};
