<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeminarsTable extends Migration
{
    public function up()
    {
        Schema::create('seminars', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('title');  // Seminar title
            $table->text('description');  // Seminar description
            $table->string('image');  // Path to the image file
            $table->dateTime('date');  // Date and time of the seminar
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('seminars');
    }
}
