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
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autor_id');
            $table->enum('autor_tipo', ['contratante', 'contratado']);
            $table->unsignedBigInteger('destinatario_id');
            $table->enum('destinatario_tipo', ['contratante', 'contratado']);
            $table->unsignedBigInteger('oferta_id');
            $table->foreign('oferta_id')->references('id')->on('ofertas')->onDelete('cascade');
            $table->integer('nota');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
