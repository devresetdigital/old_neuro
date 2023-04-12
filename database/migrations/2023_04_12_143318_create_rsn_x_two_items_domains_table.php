<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRsnXTwoItemsDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('rsn_x_two_items_domains', function (Blueprint $table) {
        $table->unsignedInteger('rsn_x_two_item_id');
        $table->unsignedInteger('domain_id');
        $table->float('score');
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
        Schema::dropIfExists('rsn_x_two_items_domains');
    }
}
