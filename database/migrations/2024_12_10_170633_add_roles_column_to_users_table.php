<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolesColumnToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Usando um campo JSON ou string. A melhor opção depende do seu caso
            // Aqui vamos usar JSON para armazenar os roles como um array
            $table->json('roles')->nullable();  // ou $table->string('roles')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
}
