<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Execute a migration.
     *
     * @return void
     */
    public function up()
    {
        // Criando a tabela funcionarios
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id(); // ID do funcionário
            $table->string('nome', 100); // Nome do funcionário
            $table->timestamps(); // created_at e updated_at
        });

        // Criando a tabela periodoFerias
        Schema::create('periodo_ferias', function (Blueprint $table) {
            $table->id(); // ID do período de férias
            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade'); // Relacionamento com funcionarios
            $table->date('inicio'); // Data de início das férias
            $table->date('fim'); // Data de fim das férias
            $table->enum('status', ['solicitado', 'confirmado'])->default('solicitado'); // Status permitido: solicitado ou confirmado
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverter a migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodo_ferias');
        Schema::dropIfExists('funcionarios');
    }
};
