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
        Schema::table('tbl_corp', function (Blueprint $table) {
            $table->enum('partnership_type', ['Market Sharing', 'Program Collaborator', 'Internship', 'External Mentor'])->nullable()->after('corp_password');
            $table->enum('type', ['Corporate', 'Individual Professional', 'Tutoring Center', 'Course Center', 'Agent', 'Community', 'NGO'])->after('corp_password');
            $table->enum('country_type', ['Indonesia', 'Overseas'])->after('corp_password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_corp', function (Blueprint $table) {
            $table->dropColumn('partnership_type');
            $table->dropColumn('type');
            $table->dropColumn('country_type');
        });
    }
};
