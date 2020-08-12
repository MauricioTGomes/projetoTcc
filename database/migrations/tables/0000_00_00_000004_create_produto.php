<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProduto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->string('apelido_produto');
            $table->decimal('qtd_estoque', 10, 2)->default(0.00);
            $table->decimal('valor_venda', 10, 2)->default(0.00);
            $table->string('codigo');
            $table->enum('ativo', array('1', '0'))->defalt('1');
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
        //
    }
}
