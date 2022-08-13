<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_pallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('container_id')->nullable();
            $table->foreign('container_id')->references('id')->on('containers');

            $table->unsignedInteger('pallet_id')->nullable();
            $table->foreign('pallet_id')->references('id')->on('pallet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_pallets');
    }
}
