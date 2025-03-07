<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('image')->nullable();
            $table->text('description');
            $table->string('email');
            $table->string('phone');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->json('certificates')->nullable();
            $table->json('gallery')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agents');
    }
};
