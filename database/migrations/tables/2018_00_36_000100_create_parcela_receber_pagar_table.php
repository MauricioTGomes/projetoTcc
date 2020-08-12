<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateParcelaReceberPagarTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('parcela_receber_pagar', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conta_id')->unsigned();
            $table->integer('nro_parcela')->unsigned();
            $table->char('baixada', 1)->default(0);
            $table->decimal('valor', 10, 2)->default(0.00);
            $table->decimal('valor_original', 10, 2)->default(0.00);
            $table->date('data_vencimento');
            $table->date('data_pagamento');
            $table->timestamps();
        });

        Schema::table('parcela_receber_pagar', function (Blueprint $table) {
            $table->foreign('conta_id')->references('id')->on('conta_receber_pagar')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('parcela_receber_pagar');
    }
}
