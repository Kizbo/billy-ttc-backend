<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model representing squares on a TTC board
 * Squares are indexed, starting from top left corner:
 * - [0, 0] [1, 0] [2, 0]
 * - [0, 1] [1, 1] [2, 1]
 * - [0, 2] [1, 2] [2, 2]
 */
class BoardSquare extends Model {
    use HasFactory;

    /**
     * Table associated with the model,
     * representing squares on the TTC board
     *
     * @var string
     */
    protected $table = "board_squares";


    public $timestamps = false;
}
