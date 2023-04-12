<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDomainsReportToRsnSignalCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rsn_signal_campaigns', function (Blueprint $table) {
            $table->text('domains_report')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rsn_signal_campaigns', function (Blueprint $table) {
            $table->dropColumn('domains_report');
        });
    }
}
