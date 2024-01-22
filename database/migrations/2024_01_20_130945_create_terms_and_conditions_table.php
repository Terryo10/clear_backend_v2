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
        Schema::create('terms_and_conditions', function (Blueprint $table) {
            $table->id();
            $table->text('contract_terms_conditions')->nullable();
            $table->text('execution_plan')->nullable();
            $table->text('scope_of_work')->nullable();
            $table->text('site_info')->nullable();
            $table->string('offer_pdf')->nullable();
            $table->string('signature')->nullable();
            $table->foreignId('selected_option')->nullable()->constrained('offer_options')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_and_conditions');
    }
};
