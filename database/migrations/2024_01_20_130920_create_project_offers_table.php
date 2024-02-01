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
        Schema::create('project_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')
                ->onDelete('cascade');
            $table->text('contract_terms_conditions')->nullable();
            $table->text('execution_plan')->nullable();
            $table->text('scope_of_work')->nullable();
            $table->text('site_info')->nullable();
            $table->string('offer_pdf')->nullable();
            $table->string('signature')->nullable();
            $table->string('manager_signature')->nullable();
            $table->foreignId('selected_option')->nullable();
            $table->enum('status', ['pending_client_sign', 'client_signed', 'manager_signed'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_offers');
    }
};
