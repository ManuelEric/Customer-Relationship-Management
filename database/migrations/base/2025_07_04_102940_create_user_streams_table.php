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
        Schema::create('user_streams', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_role_id');
            $table->foreign('user_role_id')->references('id')->on('tbl_user_roles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stream_id')->constrained(
                table: 'streams', indexName: 'user_streams_stream_id'
            )->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('engagement_type_id')->nullable()->constrained(
                table: "phase_details", indexName: "user_streams_engagement_type_id_foreign"
            )->onUpdate('cascade')->onDelete('cascade');
            $table->string('package')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('agreement')->nullable();
            $table->integer('head')->nullable();
            $table->bigInteger('additional_fee')->nullable();
            $table->string('grade', 4)->nullable();
            $table->bigInteger('fee_individual')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_streams');
    }
};
