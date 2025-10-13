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
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('city', 100);
            $table->string('coordinates', 50)->nullable();
            $table->string('address', 255);
            $table->string('phonenumber', 20)->nullable();
            $table->integer('maxroom')->nullable();
            $table->string('imagename', 100)->nullable();
            $table->boolean('isactive')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cinemas');
    }
};
