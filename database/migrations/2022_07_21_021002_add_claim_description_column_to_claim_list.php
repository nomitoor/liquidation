<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClaimDescriptionColumnToClaimList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('claim_list', function (Blueprint $table) {
            $table->text('claim_desription')->after('total_cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('claim_list', function (Blueprint $table) {
            $table->dropColumn('claim_desription');
        });
    }
}
