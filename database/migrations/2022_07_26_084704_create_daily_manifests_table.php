<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyManifestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialy_manifest', function (Blueprint $table) {
            $table->id();
            $table->string('bol')->nullable();
            $table->string('package_id')->nullable();
            $table->text('item_description')->nullable();
            $table->string('units')->nullable();
            $table->string('unit_cost')->nullable();
            $table->string('total_cost')->nullable();
            $table->string('asin')->nullable();
            $table->string('GLDesc')->nullable();
            $table->string('unit_recovery')->nullable();
            $table->string('total_recovery')->nullable();
            $table->string('recovery_rate')->nullable();
            $table->string('removal_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('DialyManifest');
    }
}