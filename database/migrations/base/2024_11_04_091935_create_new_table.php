<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Tbl_client_log used for tracking the client status
         * such as raw, new leads, potential, mentee, non-mentee, etc.
         * 
         * and this table is used in lead tracking modules
         */
        Schema::create('tbl_client_log', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 36)->collation('latin1_swedish_ci');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('category', 30);
            $table->string('lead_source', 5)->collation('latin1_swedish_ci')->nullable();
            $table->foreign('lead_source')->references('lead_id')->on('tbl_lead')->onUpdate('cascade')->onDelete('cascade');
            $table->string('inputted_from')->comment('manually input or through import');
            $table->char('unique_key', 26);
            $table->bigInteger('clientprog_id')->nullable();
            $table->foreign('clientprog_id')->references('clientprog_id')->on('tbl_client_prog')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_client_log');
    }
};
