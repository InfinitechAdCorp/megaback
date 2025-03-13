<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('client_properties', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('email');
            $table->string('number');
            $table->string('property_name');
            $table->string('development_type');
            $table->json('unit_type')->nullable();
            $table->string('price');
            $table->string('status');
            
            $table->string('location');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('client_properties');
    }
};

