<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url')->nullable(); // Store YouTube or external video URL
            $table->string('file_path')->nullable(); // Store uploaded video file path
            $table->string('thumbnail')->nullable(); // Store thumbnail image
            $table->string('location')->nullable();
            $table->date('date')->nullable();
            $table->string('views')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('videos');
    }
};
