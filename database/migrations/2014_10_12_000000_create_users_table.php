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
        #based on employee table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->unique()->nullable();
            $table->string('extended_id', 15)->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('emergency_contact', 25)->nullable();
            $table->date('datebirth')->nullable();

            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('tbl_department')->onUpdate('cascade')->onDelete('cascade');

            $table->string('password')->nullable();
            $table->date('hiredate')->nullable();
            $table->bigInteger('nik')->nullable();
            $table->text('idcard')->nullable();
            $table->string('cv')->nullable();
            $table->string('bankname', 50)->nullable();
            $table->string('bankacc', 25)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->text('tax')->nullable();
            $table->boolean('active')->nullable();
            $table->string('health_insurance')->nullable();
            $table->string('empl_insurance')->nullable();
            $table->boolean('export')->default(1);
            $table->text('notes')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
