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
        Schema::create('supervisions', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('link');
            $table->json('image')->nullable();
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('violation_id')->nullable()->constrained();
            $table->foreignId('keyword_id')->constrained();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('sold');
            $table->float('rating');
            $table->foreignId('marketplace_id')->constrained();
            $table->string('seller');
            $table->json('location');
            $table->boolean('check')->default(false);
            $table->dateTime('last_check')->nullable();
            $table->boolean('status')->default(false);
            $table->dateTime('solved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisions');
    }
};
