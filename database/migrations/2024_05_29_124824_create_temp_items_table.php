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
        Schema::create('temp_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('image')->nullable();
            $table->unsignedBigInteger('price');
            $table->float('rating');
            $table->unsignedInteger('sold');
            $table->string('seller');
            $table->string('location');
            $table->text('link');
            $table->foreignId('comodity_id')->constrained();
            $table->foreignId('marketplace_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_items');
    }
};
