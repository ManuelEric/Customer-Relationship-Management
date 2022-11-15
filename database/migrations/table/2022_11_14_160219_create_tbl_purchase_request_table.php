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
        Schema::create('tbl_purchase_request', function (Blueprint $table) {
            $table->char('purchase_id', 8)->primary()->collation('utf8mb4_general_ci');
            $table->string('requested_by');
            $table->string('purchase_department');
            $table->string('purchase_usedfor');
            $table->string('purchase_statusrequest');
            $table->text('purchase_notes')->nullable();
            $table->text('purchase_attachment')->nullable();
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
        Schema::dropIfExists('tbl_purchase_request');
    }
};
