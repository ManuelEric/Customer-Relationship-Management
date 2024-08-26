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
        Schema::create('tbl_inv_attachment', function (Blueprint $table) {
            $table->id();

            $table->char('inv_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('inv_id')->references('inv_id')->on('tbl_inv')->onUpdate('cascade')->onDelete('cascade');

            $table->char('invb2b_id', 50)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('invb2b_id')->references('invb2b_id')->on('tbl_invb2b')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('currency', ['idr', 'other']);
            $table->enum('sign_status', ['not yet', 'signed'])->default('not yet');
            $table->date('approve_date')->nullable();
            $table->enum('send_to_client', ['not sent', 'sent'])->default('not sent');
            $table->text('attachment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_inv_attachment');
    }
};
