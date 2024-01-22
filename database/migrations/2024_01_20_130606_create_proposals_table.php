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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->double('cost');
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('attachment')->nullable();
            $table->text('description')->nullable();
            $table->text('contract_terms_conditions')->nullable();
            $table->text('execution_plan')->nullable();
            $table->text('site_info')->nullable();
            $table->text('scope_of_work')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
