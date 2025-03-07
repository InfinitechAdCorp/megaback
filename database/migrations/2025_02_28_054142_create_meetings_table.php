<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('title');  // Meeting title
            $table->text('description');  // Meeting description
            $table->string('image');  // Path to the image file
            $table->dateTime('date');  // Date and time of the meeting
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('meetings');
    }
}
