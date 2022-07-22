<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToScannedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scanned_products', function (Blueprint $table) {
            $table->string('asin')->after('total_cost')->nullable();
            $table->string('GLDesc')->after('asin')->nullable();
            $table->string('unit_recovery')->after('GLDesc')->nullable();
            $table->string('total_recovery')->after('unit_recovery')->nullable();
            $table->string('recovery_rate')->after('total_recovery')->nullable();
            $table->string('removal_reason')->after('recovery_rate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scanned_products', function (Blueprint $table) {
            //
        });
    }
}
