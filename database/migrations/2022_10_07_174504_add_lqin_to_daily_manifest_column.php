<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLqinToDailyManifestColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dialy_manifest', function (Blueprint $table) {
            $table->string('lqin')->nullable();
        });
        Schema::table('manifests', function (Blueprint $table) {
            $table->string('lqin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_manifest_column', function (Blueprint $table) {
            //
        });
    }
}
