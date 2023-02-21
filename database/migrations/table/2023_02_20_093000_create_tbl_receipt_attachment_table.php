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
        Schema::create('tbl_receipt_attachment', function (Blueprint $table) {
            $table->id();

            $table->char('receipt_id', 50)->collation('utf8mb4_general_ci');
            $table->foreign('receipt_id')->references('receipt_id')->on('tbl_receipt')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('tbl_receipt_attachment');
    }
};
