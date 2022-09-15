<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpnColumnToDailyAndWeeklyManifest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dialy_manifest', function (Blueprint $table) {
            $table->string('lpn');
        });

        Schema::table('manifests', function (Blueprint $table) {
            $table->string('lpn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dialy_manifest', function (Blueprint $table) {
            $table->string('lpn');
        });
        Schema::table('manifests', function (Blueprint $table) {
            $table->string('lpn');
        });
    }
}
