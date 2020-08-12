<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePessoa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

		Schema::create('pessoa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cidade_id')->unsigned();
            $table->string('razao_social')->default(null);
            $table->string('fantasia')->default(null);
            $table->string('nome')->default(null);
            $table->string('cpf')->default(null);
            $table->string('cnpj')->default(null);
            $table->string('ie')->default(null);
            $table->string('rg')->default(null);
            $table->string('email')->default(null);
            $table->string('endereco')->default(null);
            $table->string('cep')->default(null);
            $table->integer('endereco_nro')->default(null);
            $table->string('bairro')->default(null);
            $table->string('complemento')->default(null);
            $table->string('fone')->default(null);
            $table->enum('ativo', array('1', '0'))->defalt('1');
            $table->enum('cliente', array('1', '0'))->defalt('1');
            $table->enum('fornecedor', array('1', '0'))->defalt('1');
            $table->enum('tipo', array('FISICO', 'JURIDICO'))->defalt('FISICO');
            $table->timestamps();
        });

        Schema::table('pessoa', function ($table) {
            $table->foreign('cidade_id')->references('id')->on('cidade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
