<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResponsavelLegalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responsavel_legal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->text('estado_civil');
            $table->string('profissao');
            $table->string('rg');
            $table->string('cpf');
            $table->string('endereco');
            $table->string('gestao');
            $table->string('assinatura');
            $table->string('localizacao_sede');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('responsavel_legal');
    }
}
