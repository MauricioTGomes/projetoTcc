<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlteraUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function ($table) {
            $table->enum('tipo', ['GERENTE', 'VENDEDOR'])->nullable()->default('GERENTE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function ($table) {
            $table->dropColumn('tipo');
        });
    }
}
