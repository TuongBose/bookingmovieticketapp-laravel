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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('phonenumber', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->date('dateofbirth');
            $table->string('imagename', 100)->nullable();
            $table->dateTime('createdat')->nullable();
            $table->boolean('isactive')->default(true);
            $table->boolean('rolename')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
