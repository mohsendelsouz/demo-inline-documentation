<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->float('truck_regular_maintenance_millage')->nullable();
            $table->float('truck_major_maintenance_millage')->nullable();
            $table->string('truck_inspection')->nullable();
            $table->float('machine_regular_maintenance_hour')->nullable();
            $table->float('machine_major_maintenance_hour')->nullable();
            $table->string('machine_inspection')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
