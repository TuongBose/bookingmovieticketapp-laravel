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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->id(); 

            $table->foreignId('movieid') 
                  ->constrained('movies') 
                  ->onDelete('cascade'); 

            $table->foreignId('roomid') 
                  ->constrained('rooms') 
                  ->onDelete('cascade'); 

            $table->date('showdate'); 
            $table->dateTime('starttime'); 
            $table->integer('price');
            $table->boolean('isactive')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
