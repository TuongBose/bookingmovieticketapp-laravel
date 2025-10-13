<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('movieid')
                ->constrained('movies')
                ->onDelete('cascade');

            $table->foreignId('userid')
                ->constrained('users')
                ->onDelete('cascade');

            $table->unsignedTinyInteger('rating')
                ->check('rating >= 1 AND rating <= 10')
                ->comment('rating value from 1 to 10');
                
            $table->string('comment', 255)->nullable();
            $table->dateTime('createdat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
