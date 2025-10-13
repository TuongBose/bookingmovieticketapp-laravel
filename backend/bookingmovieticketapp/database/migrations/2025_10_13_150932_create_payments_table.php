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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); 

            $table->foreignId('bookingid') 
                  ->constrained('bookings')
                  ->onDelete('cascade'); 

            $table->integer('totalprice'); 
            $table->string('paymentmethod', 50); 
            $table->string('paymentstatus', 50); 
            $table->dateTime('paymenttime'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
