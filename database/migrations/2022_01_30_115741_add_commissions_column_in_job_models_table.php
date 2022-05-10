<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionsColumnInJobModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_models', function (Blueprint $table) {
            $table->float('wows')->default(0)->after('tip');
            $table->float('technician_commission')->default(0)->after('wows');
            $table->float('sales_commission')->default(0)->after('technician_commission');
            $table->float('operational_manager_commission')->default(0)->after('sales_commission');
            $table->float('general_manager_commission_commission')->default(0)->after('operational_manager_commission');

            $table->float('sales_commission_percentage')->default(0)->after('payment_received_at');
            $table->float('operational_manager_commission_percentage')->default(0)->after('sales_commission_percentage');
            $table->float('general_manager_commission_percentage')->default(0)->after('operational_manager_commission_percentage');
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
            $table->dropColumn('wows');
            $table->dropColumn('technician_commission');
            $table->dropColumn('sales_commission');
            $table->dropColumn('operational_manager_commission');
            $table->dropColumn('general_manager_commission_commission');
            $table->dropColumn('sales_commission_percentage');
            $table->dropColumn('operational_manager_commission_percentage');
            $table->dropColumn('general_manager_commission_percentage');
        });
    }
}
