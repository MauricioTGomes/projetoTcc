<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateItemTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('produto_id')->unsigned();
            $table->integer('pedido_id')->unsigned();
            $table->decimal('valor_total', 10, 2)->default(0.00);
            $table->decimal('valor_unitario', 10, 2)->default(0.00);
            $table->char('quantidade', 255)->default(1.00);
            $table->timestamps();
        });

        Schema::table('item', function (Blueprint $table) {
            $table->foreign('produto_id')->references('id')->on('produto');
            $table->foreign('pedido_id')->references('id')->on('pedido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('item');
    }
}
