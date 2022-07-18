<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePalletColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pallet', function (Blueprint $table) {
            $table->longText('bol_ids')->nullable()->change();
            $table->string('total_price')->nullable()->change();
            $table->string('total_unit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pallet', function (Blueprint $table) {
            //
        });
    }
}
