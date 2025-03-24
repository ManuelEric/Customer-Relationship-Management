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
        Schema::create('failed_meta_leads', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone', 15)->nullable();
            $table->string('parent_email')->nullable();
            $table->string('child_name')->nullable();
            $table->string('child_graduation_year')->nullable();
            $table->string('child_school')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_meta_leads');
    }
};
