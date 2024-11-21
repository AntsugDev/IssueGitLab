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
        Schema::create('labels',function (Blueprint $table){
            $table->id()->autoIncrement();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('description_html')->nullable();
            $table->string('text_color')->nullable();
            $table->string('color')->nullable();
            $table->string('subscribed')->nullable();
            $table->unsignedBigInteger('priority')->nullable();
            $table->boolean('is_project_label')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labels');
    }
};
