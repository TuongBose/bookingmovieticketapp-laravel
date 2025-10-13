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
        Schema::create('bookings', function (Blueprint $table) {
             $table->id(); 

            $table->foreignId('userid') 
                  ->constrained('users') 
                  ->onDelete('cascade'); 

            $table->foreignId('showtimeid')
                  ->constrained('showtimes')
                  ->onDelete('cascade'); 

            $table->dateTime('bookingdate'); 
            $table->integer('totalprice');
            $table->string('paymentmethod', 50); 
            $table->string('paymentstatus', 50); 
            $table->boolean('isactive')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
