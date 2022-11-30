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
        Schema::table('tbl_sch_prog', function (Blueprint $table) {
            $table->date('denied_date')->nullable()->after('notes');
            $table->string('reason')->nullable()->after('notes');
            $table->date('success_date')->nullable()->after('notes');
            $table->date('start_program_date')->nullable()->after('notes');
            $table->date('end_program_date')->nullable()->after('notes');
            $table->string('place')->nullable()->after('notes');
            $table->integer('participants')->nullable()->after('notes');
            $table->double('total_fee')->nullable()->after('notes');
            $table->integer('total_hours')->nullable()->after('notes');
            $table->enum('running_status', ['Pending', 'Success', 'Denied'])->nullable()->after('notes');
            $table->text('notes_detail')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_sch_prog', function (Blueprint $table) {
            //
        });
    }
};
