<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferredColumnInJobModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_models', function (Blueprint $table) {
            $table->boolean('referred')->default(0)->after('client_email');
            $table->unsignedBigInteger('referral_id')->nullable()->after('referred');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_models', function (Blueprint $table) {
            $table->dropColumn('referred');
            $table->dropColumn('referral_id');
        });
    }
}
