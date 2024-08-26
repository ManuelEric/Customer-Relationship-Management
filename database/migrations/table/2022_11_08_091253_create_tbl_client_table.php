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
        Schema::create('tbl_client', function (Blueprint $table) {
            $table->id();
            $table->char('st_id', 7)->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('mail');
            $table->string('phone', 22)->nullable();
            $table->date('dob')->nullable();
            $table->string('insta')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            # foreign key school id
            $table->char('sch_id', 8)->collation('utf8mb4_general_ci');
            $table->foreign('sch_id')->references('sch_id')->on('tbl_sch')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('st_grade');
            # foreign key lead id
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('tbl_lead')->onUpdate('cascade')->onDelete('cascade');
            # foreign key eduf id
            # will filled if lead id is edufair
            $table->unsignedBigInteger('eduf_id')->nullable();
            $table->foreign('eduf_id')->references('id')->on('tbl_eduf_lead')->onUpdate('cascade')->onDelete('cascade');
            $table->string('st_levelinterest');
            # foreign key program id
            $table->char('prog_id', 11)->collation('utf8mb4_general_ci')->nullable();
            $table->foreign('prog_id')->references('prog_id')->on('tbl_prog')->onUpdate('cascade')->onDelete('restrict');
            $table->char('graduation_year', 4)->nullable();
            $table->char('st_abryear', 4)->nullable();
            $table->text('st_abrcountry')->nullable();
            $table->boolean('st_statusact')->default(1)->comment('status aktif client');
            $table->text('st_note')->nullable();
            $table->tinyInteger('st_statuscli')->default(0)->comment('0: prospective, 1: potential, 2: current, 3: completed');
            $table->string('st_password');
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
        Schema::dropIfExists('tbl_client');
    }
};
