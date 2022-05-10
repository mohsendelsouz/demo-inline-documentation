<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTechniciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_technicians', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('job_model_id');
            $table->unsignedInteger('technician_id');
            $table->float('wow')->default(0);
            $table->string('wow_type')->nullable();
            $table->float('tip')->default(0);
            $table->float('reliable')->default(0);
            $table->float('team_player')->default(0);
            $table->float('integrity')->default(0);
            $table->float('great_communicator')->default(0);
            $table->float('proactive')->default(0);
            $table->float('avg_sc')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_technicians');
    }
}
