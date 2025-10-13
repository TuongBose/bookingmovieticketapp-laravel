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
        Schema::create('movies', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); 
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->date('releasedate');
            $table->string('posterurl', 255)->nullable();
            $table->string('bannerurl', 255)->nullable();
            $table->string('agerating', 10)->nullable();
            $table->decimal('voteaverage', 3, 1);
            $table->string('director', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
