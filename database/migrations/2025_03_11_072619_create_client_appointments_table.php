<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('client_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('property_name'); // Keep this for identifying the property
            $table->string('name');
            $table->string('email');
            $table->string('contact_number'); // Ensure this is a string and non-nullable
            $table->dateTime('date');
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // Add status column, default 'pending'
            $table->string('type'); // 'type' column to distinguish between 'Request Viewing' or 'Inquiry'
            $table->timestamps();

            // Foreign key constraint (optional, if properties table exists)
            // $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_appointments');
    }
};
