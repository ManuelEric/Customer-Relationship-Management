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
        Schema::create('contribution_calculation_tmp', function (Blueprint $table) {
            $table->id();
            $table->string('divisi');
            $table->double('contribution_in_percent');
            $table->integer('contribution_to_target')->nullable();
            $table->integer('initial_consult_target')->comment('# of IC (1,5x target)')->nullable();
            $table->integer('hot_leads_target')->comment('# of hot leads (2x IC)')->nullable();
            $table->integer('leads_needed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contribution_calculation_tmp');
    }
};
