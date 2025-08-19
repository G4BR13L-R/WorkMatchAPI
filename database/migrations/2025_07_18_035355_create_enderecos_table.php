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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('logradouro', 255)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 255)->nullable();
            $table->string('bairro', 255)->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable();
            $table->foreign('cidade_id')->references('id')->on('cidades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
