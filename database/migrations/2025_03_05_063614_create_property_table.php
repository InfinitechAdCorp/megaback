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
    Schema::create('properties', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description');
        $table->string('location');
        $table->string('specificLocation')->nullable();
        $table->string('status');
        $table->string('priceRange');
        $table->string('lotArea');
        $table->json('units')->nullable();
        $table->json('amenities')->nullable();
        $table->json('features')->nullable(); // ✅ Add features column
        $table->string('image')->nullable();
        $table->string('masterPlan')->nullable();
        $table->string('developmentType')->nullable();
        $table->integer('floors')->nullable();
        $table->integer('parkingLots')->nullable();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
