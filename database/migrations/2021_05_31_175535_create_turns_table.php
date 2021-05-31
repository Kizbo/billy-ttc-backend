<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurnsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('turns', function (Blueprint $table) {
            $table->id();
            $table->string("turn_symbol");
            $table->unsignedBigInteger('square_id');
            $table->foreign("square_id")->references("id")->on("board_squares");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('turns');
    }
}
