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
            $table->string('pallets_id')->nullable()->change();
            $table->longText('bol_ids')->nullable()->change();
            $table->string('total_price')->nullable()->change();
            $table->string('total_unit')->nullable()->change();
            $table->string('description')->nullable()->after('pallets_id');
            $table->foreignId('category_id')->nullable()->constrained('categories')->after('id');
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
            $table->dropForeign(['category_id']);
            $table->dropColumn(['description', 'category_id']);
        });
    }
}
