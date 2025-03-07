<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Ensure each location has a unique name
            $table->timestamps();  // Automatically includes 'created_at' and 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
