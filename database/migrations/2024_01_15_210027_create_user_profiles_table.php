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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('gender')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_country')->nullable();
            $table->string('home_state')->nullable();
            $table->string('home_zip_code')->nullable();
            $table->string('home_address')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('work_title')->nullable();
            $table->string('company_name')->nullable();
            $table->string('work_phone')->nullable();
            $table->string('work_address')->nullable();
            $table->string('work_city')->nullable();
            $table->string('work_state')->nullable();
            $table->string('work_zip_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
