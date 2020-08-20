<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContasReceberPagarsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('conta_receber_pagar', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pessoa_id')->unsigned()->default(null);
            $table->integer('user_id')->unsigned();
            $table->integer('pedido_id')->unsigned()->default(null);
            $table->char('tipo_operacao', 1)->default('R'); //P ou R);
            $table->decimal('vlr_total', 10, 2)->default(0.00);
            $table->decimal('vlr_restante', 10, 2)->default(0.00);
            $table->string('titulo')->index();
            $table->date('data_emissao');
            $table->integer('qtd_dias');
            $table->timestamps();
        });

        Schema::table('conta_receber_pagar', function ($table) {
            $table->foreign('pessoa_id')->references('id')->on('pessoa');
            $table->foreign('pedido_id')->references('id')->on('pedido')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('conta_receber_pagar');
    }
}
