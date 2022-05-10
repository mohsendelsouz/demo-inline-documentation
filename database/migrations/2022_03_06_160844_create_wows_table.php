<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_model_id');
            $table->string('type');
            $table->unsignedInteger('charity_id')->nullable();
            $table->float('amount');
            $table->string('review');
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
        Schema::dropIfExists('wows');
    }
}
