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
        Schema::table('job_batches', function (Blueprint $table) {
            $table->enum('type', ['student', 'parent', 'teacher', 'client-event', 'client-program'])->nullable()->after('finished_at');
            $table->integer('total_data')->default('0')->after('finished_at');
            $table->integer('total_imported')->default('0')->after('finished_at');
            $table->longText('log_details')->nullable()->after('finished_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_batches', function (Blueprint $table) {
            //
        });
    }
};
