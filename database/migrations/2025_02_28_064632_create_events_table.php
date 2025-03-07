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
        Schema::create('events', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('title');  // Meeting title
            $table->text('description');  // Meeting description
            $table->string('image')->nullable();  // Path to the image file (nullable for video)
            $table->string('file')->nullable();  // Path to the video file (nullable for image)
            $table->enum('media_type', ['image', 'video']);  // Media type (image or video)
            $table->dateTime('date');  // Date and time of the meeting
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
