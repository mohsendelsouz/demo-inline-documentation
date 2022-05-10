<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoalsColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->float('wows_goal')->default(0)->after('commission_wallet');
            $table->float('scorecard_goal')->default(0)->after('wows_goal');
            $table->float('pay_goal')->default(0)->after('scorecard_goal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wows_goal');
            $table->dropColumn('scorecard_goal');
            $table->dropColumn('pay_goal');
        });
    }
}
