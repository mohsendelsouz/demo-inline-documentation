<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultPercentageColumnInJobTechniciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_technicians', function (Blueprint $table) {
            $table->float('default_percentage')->default(0)->after('technician_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_technicians', function (Blueprint $table) {
            $table->dropColumn('default_percentage');
        });
    }
}
