<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateManifestColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scanned_products', function (Blueprint $table) {
            $table->string('package_id')->nullable()->change();
            $table->string('bol')->nullable()->change();
            $table->longText('item_description')->nullable()->change();
            $table->string('units')->nullable()->change();
            $table->string('unit_cost')->nullable()->change();
            $table->string('total_cost')->nullable()->change();
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
