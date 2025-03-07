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
    Schema::create('offices', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description');
        $table->string('image')->nullable();
        $table->string('location');
        $table->enum('status', ['For Lease', 'For Sale', 'For Rent']);
        $table->string('price');
        $table->string('lotArea');
        $table->json('amenities')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
