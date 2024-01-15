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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            //address details
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->double('budget')->nullable();
            $table->string('frequency')->default('Monthly');
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('key_factor')->default('quality');
            $table->string('status')->default('pending');
            $table->text('additionalRequirements')->nullable();
            $table->boolean('is_drafted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
