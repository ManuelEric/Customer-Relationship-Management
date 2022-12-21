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
        Schema::create('tbl_client_prog', function (Blueprint $table) {
            $table->unsignedBigInteger('clientprog_id')->autoIncrement();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('tbl_client')->onUpdate('cascade')->onDelete('restrict');

            $table->char('prog_id', 11)->collation('utf8mb4_general_ci');
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('restrict');

            $table->string('lead_id', 5);
            $table->foreign('lead_id')->references('lead_id')->on('tbl_lead')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('eduf_lead_id')->nullable();
            $table->foreign('eduf_lead_id')->references('id')->on('tbl_eduf_lead')->onUpdate('cascade')->onDelete('restrict');

            $table->char('partner_id', 9)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('partner_id')->references('corp_id')->on('tbl_corp')->onUpdate('cascade')->onDelete('restrict');

            $table->unsignedBigInteger('clientevent_id')->nullable();
            $table->foreign('clientevent_id')->references('clientevent_id')->on('tbl_client_event')->onUpdate('cascade')->onDelete('cascade');

            $table->date('first_discuss_date')->nullable();
            $table->date('last_discuss_date')->nullable();
            $table->date('followup_date')->nullable();
            $table->date('meeting_date')->nullable();
            $table->text('meeting_notes')->nullable();
            $table->integer('status')->default(0)->comment('0: pending, 1: success, 2: failed, 3: refund');
            $table->date('statusprog_date')->nullable();
            $table->date('initconsult_date')->nullable();
            $table->date('assessmentsent_date')->nullable();
            $table->date('negotiation_date')->nullable();
            
            $table->unsignedBigInteger('reason_id')->nullable();
            $table->foreign('reason_id')->references('reason_id')->on('tbl_reason')->onUpdate('cascade')->onDelete('cascade');

            $table->date('test_date')->nullable();
            $table->date('last_class')->nullable();
            $table->integer('diag_score')->default(0);
            $table->integer('test_score')->default(0);
            $table->bigInteger('price_from_tutor')->default(0);
            $table->bigInteger('our_price_tutor')->default(0);
            $table->bigInteger('total_price_tutor')->default(0);

            $table->text('duration_notes')->nullable();
            $table->integer('total_uni')->default(0);
            $table->bigInteger('total_foreign_currency')->default(0);
            $table->integer('foreign_currency_exchange')->default(0);
            $table->string('foreign_currency', 20)->nullable();
            $table->bigInteger('total_idr')->default(0);
            $table->text('installment_notes')->nullable();
            $table->integer('prog_running_status')->default(0)->comment('0: not yet, 1: ongoing, 2: done');
            $table->date('prog_start_date')->nullable();
            $table->date('prog_end_date')->nullable();

            $table->unsignedBigInteger('empl_id');
            $table->foreign('empl_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');


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
        Schema::dropIfExists('tbl_client_prog');
    }
};
