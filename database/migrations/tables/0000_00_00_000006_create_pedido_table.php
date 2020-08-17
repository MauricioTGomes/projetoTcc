<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePedidoTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('pedido', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pessoa_id')->unsigned();
            $table->integer('user_abertura_id')->unsigned();
            $table->integer('user_estorno_id')->default(null);
            $table->decimal('valor_total', 10, 2)->default(0.00);
            $table->decimal('valor_desconto', 10, 2)->default(0.00);
            $table->decimal('valor_liquido', 10, 2)->default(0.00);
            $table->string('observacoes', 255);
            $table->char('status', 1)->default(0);
            $table->char('numero', 255);
            $table->date('data_entrega');
            $table->timestamps();
        });

        Schema::table('pedido', function (Blueprint $table) {
            $table->foreign('pessoa_id')->references('id')->on('pessoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('pedido');
    }
}
