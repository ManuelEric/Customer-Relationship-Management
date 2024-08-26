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
        Schema::table('tbl_partner_prog', function (Blueprint $table) {
            $table->unsignedBigInteger('reason_id')->nullable()->after('end_date');

            $table->foreign('reason_id')->references('reason_id')->on('tbl_reason')->onUpdate('cascade')->onDelete('cascade');
            
            $table->tinyInteger('is_corporate_scheme')->after('end_date');
            $table->double('total_fee')->nullable()->after('end_date');
            $table->date('success_date')->nullable()->after('end_date');
            $table->date('denied_date')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_partner_prog', function (Blueprint $table) {
            //
        });
    }
};
