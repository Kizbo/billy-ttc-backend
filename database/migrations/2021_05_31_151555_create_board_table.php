<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBoardTable extends Migration {


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        /**
         * Create board table
         */
        Schema::create('board_squares', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger("x_cord");
            $table->tinyInteger("y_cord");
            $table->enum("square_value", ['x', 'o'])->nullable();
        });

        /**
         * Insert squares to the board
         */

        DB::table("board_squares")->upsert($this->getBoardSquares(), ["x_cord", "y_cord"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('board_squares');
    }


    /**
     * Get array defining squares on the TTC board
     *
     * @return array
     */
    public function getBoardSquares(): array {
        $squares = [];

        for ($x = 0; $x < 3; $x++) {
            for ($y = 0; $y < 3; $y++) {
                $squares[] = [
                    'x_cord'        =>  $x,
                    'y_cord'        =>  $y,
                    'square_value'  =>  null,
                ];
            }
        }

        return $squares;
    }
}
