<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('position')->nullable();
            $table->string('type')->nullable();
            $table->boolean('active')->nullable();
            $table->float('default_percentage')->default(0);
            $table->float('sales_commission')->default(0);
            $table->float('operational_manager_commission')->default(0);
            $table->float('general_manager_commission')->default(0);
            $table->float('wow_wallet')->default(0);
            $table->float('tip_wallet')->default(0);
            $table->float('commission_wallet')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
