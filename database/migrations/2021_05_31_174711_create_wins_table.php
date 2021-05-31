<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWinsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('wins', function (Blueprint $table) {
            $table->id();
            $table->enum("board_symbol", ["x", "o"]);
            $table->integer("wins_count");
        });


        DB::table("wins")->upsert($this->defineSymbolsRows(), ['board_symbol']);
    }

    /**
     * Return default rows defining symbols and their wins count
     *
     * @return array
     */
    public function defineSymbolsRows() {
        return [
            [
                'board_symbol'  =>  'x',
                'wins_count'    =>  0,
            ],
            [
                'board_symbol'  =>  'o',
                'wins_count'    =>  0,
            ]
        ];
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('wins');
    }
}
