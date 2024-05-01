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
        Schema::create('vendor_project_submits', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_contact')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('website')->nullable();
            $table->string('company_business_entity')->nullable();
            $table->string('company_establishment_date')->nullable();
            $table->string('brief_description_of_your_company')->nullable();
            $table->string('service_coverage_area')->nullable();
            $table->string('number_of_personnel_on_staff')->nullable();
            $table->string('company_licences_certifications_or_awards')->nullable();
            $table->string('types_of_commercial_properties_currently_served')->nullable();
            $table->string('commercial_building_services_offered')->nullable();
            $table->string('how_did_you_hear_about_us')->nullable();
            $table->string('does_your_company_have_general_liability_insurance')->nullable();
            $table->string('does_your_company_have_workers_compensation_insurance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_project_submits');
    }
};
