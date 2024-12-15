<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamRoleAndDaysToVacationRequests extends Migration
{
    public function up()
    {
        Schema::table('vacation_requests', function (Blueprint $table) {
            // Adicionando o campo 'team' para armazenar o nome da equipe do usuário
            $table->string('team')->nullable();  // O campo 'team' pode ser nulo, caso o usuário não tenha uma equipe

            // Adicionando o campo 'role' para armazenar o papel do usuário
            $table->string('role')->nullable();  // O campo 'role' pode ser nulo, caso o usuário não tenha uma role definida

            // Adicionando o campo 'days' para armazenar os dias solicitados como string
            $table->text('days')->nullable();  // Usando 'text' para armazenar os dias como uma string (ex: "2025-01-01,2025-01-02")
        });
    }

    public function down()
    {
        Schema::table('vacation_requests', function (Blueprint $table) {
            // Remover os campos se a migração for revertida
            $table->dropColumn('team');
            $table->dropColumn('role');
            $table->dropColumn('days');
        });
    }
}
