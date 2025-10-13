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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id(); 

            $table->foreignId('bookingid')
                  ->constrained('bookings') 
                  ->onDelete('cascade'); 

            $table->foreignId('seatid') 
                  ->constrained('seats')
                  ->onDelete('cascade');

            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
