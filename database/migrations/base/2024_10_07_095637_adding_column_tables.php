<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->integer('status')->comment('0: pending, 1: success, 2: failed, 3: refund, 4: hold')->change();
            $table->datetime('hold_date')->comment('a date that created to inform when holding process started')->after('empl_id')->nullable();
            $table->timestamp('agreement_uploaded_at')->nullable()->after('agreement');
        });

        Schema::table('tbl_lead', function (Blueprint $table) {
            $table->string('description')->nullable()->after('sub_lead');
            $table->boolean('is_online')->default(false)->after('sub_lead');
            $table->string('type', 50)->after('sub_lead')->nullable(); 
        });

        Schema::table('tbl_client', function (Blueprint $table) {
            $table->timestamp('took_ia_date')->nullable()->after('category');
        });

        Schema::table('tbl_client', function (Blueprint $table) {
            DB::statement("ALTER TABLE tbl_client CHANGE COLUMN category category ENUM('trash', 'raw', 'new-lead', 'potential', 'mentee', 'non-mentee', 'alumni-mentee', 'alumni-non-mentee') NULL");
        });

        # create a relation for column country_id
        Schema::table('tbl_client_abrcountry', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('tbl_country')->onUpdate('cascade')->onDelete('cascade');
        });

        /**
         * this some special case that happened when there are mentees that need to be stored in CRM while the information is not well provided
         * because of that, user_id in tbl_client_mentor will be accepting null in order to gave admin a hint that when client_id have no user_id in tbl_client_mentor
         * meaning they are forced to be stored in CRM
         */
        Schema::table('tbl_client_mentor', function (Blueprint $table) {
            $table->string('user_id', 36)->nullable()->comment('whoever client that have no user_id means they are just stored for archive because no information provided')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_client_prog', function (Blueprint $table) {
            $table->dropColumn('hold_date');
            $table->dropColumn('agreement_uploaded_at');
        });

        Schema::table('tbl_client', function (Blueprint $table) {
            $table->dropColumn('took_ia_date');
        });
    }
};