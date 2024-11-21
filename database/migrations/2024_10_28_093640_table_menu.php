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
        Schema::create('menu', function (Blueprint $table) {
        $table->id()->autoIncrement();
        $table->string('name');
        $table->string('route_name');
        $table->string('icon')->nullable();
        $table->unsignedBigInteger('child')->nullable();
        $table->string('is_general')->default('All');
        $table->timestamps();
        $table->softDeletes();

        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
