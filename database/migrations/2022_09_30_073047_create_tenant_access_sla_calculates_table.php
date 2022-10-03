<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql-karawang')->create('tenant_access_sla_calculates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("ticket_no");
            $table->time("time_required_by_admin_to_dispatch")->nullable();
            $table->time("time_required_by_agent_to_response")->nullable();
            $table->time("time_required_by_agent_to_complete")->nullable();
            $table->time("time_required_total")->nullable();
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
        Schema::dropIfExists('tenant_access_sla_calculates');
    }
};
