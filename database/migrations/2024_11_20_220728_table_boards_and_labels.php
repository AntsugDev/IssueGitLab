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
        Schema::create('boards_and_labels',function (Blueprint $table){
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('id_boards');
            $table->foreign('id_boards','board_vs_labels')->on('boards')->references('id');
            $table->unsignedBigInteger('id_labels');
            $table->foreign('id_labels','labels_a_vs_oards')->on('labels')->references('id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards_and_labels');
    }
};
