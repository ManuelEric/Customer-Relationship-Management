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