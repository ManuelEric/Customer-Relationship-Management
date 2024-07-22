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
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            $table->renameColumn('fee_hours', 'fee_individual');
            $table->renameColumn('fee_session', 'fee_group')->nullable()->change();
            $table->string('grade')->after('subject_id');
            $table->bigInteger('additional_fee')->after('subject_id')->nullable();
            $table->integer('head')->after('subject_id')->nullable();
            $table->text('agreement')->after('subject_id');
            $table->year('year')->after('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_user_subjects', function (Blueprint $table) {
            //
        });
    }
};
