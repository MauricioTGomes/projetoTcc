<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMoviCaixaTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('movimentacao_caixa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pedido_id')->unsigned()->default(null);
            $table->integer('user_id')->unsigned();
            $table->integer('parcela_id')->unsigned()->default(null);
            $table->decimal('valor', 10, 2)->default(0.00);
            $table->string('descricao', 255);
            $table->char('operacao', 1)->default(0);
            $table->enum('movimentacao', ['ENTRADA', 'SAIDA'])->default('ENTRADA');
            $table->timestamps();
        });

        Schema::table('movimentacao_caixa', function (Blueprint $table) {
            $table->foreign('pedido_id')->references('id')->on('pedido');
            $table->foreign('parcela_id')->references('id')->on('parcela_receber_pagar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('movimentacao_caixa');
    }
}
