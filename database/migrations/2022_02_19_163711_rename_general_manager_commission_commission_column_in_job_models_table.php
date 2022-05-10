<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameGeneralManagerCommissionCommissionColumnInJobModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_models', function (Blueprint $table) {
            $table->renameColumn('general_manager_commission_commission', 'general_manager_commission');
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
            $table->renameColumn('general_manager_commission', 'general_manager_commission_commission');
        });
    }
}
