<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_models', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->unsignedInteger('company_id');
            $table->string('invoice_no');
            $table->unsignedInteger('truck_id');
            $table->unsignedInteger('machine_id');
            $table->string('client');
            $table->string('payment_method')->nullable();
            $table->date('payment_received_at')->nullable();
            $table->float('tip')->default(0);
            $table->float('amount')->default(0);
            $table->unsignedInteger('truck_technician_id');
            $table->unsignedInteger('sales_person_id')->nullable();
            $table->unsignedInteger('operational_manager_id')->nullable();
            $table->unsignedInteger('general_manager_id')->nullable();
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
        Schema::dropIfExists('job_models');
    }
}
