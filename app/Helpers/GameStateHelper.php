<?php

namespace App\Helpers;

use App\Models\BoardSquare;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Wrapper for functions returning information about the game current state
 */
class GameStateHelper {

    const X_PIECE = 'x';
    const O_PIECE = 'o';
    const EMPTY_PIECE = '';


    public static function getGameState() {

        $winner = GameStateHelper::checkIfThereIsWinner();

        return [
            'board' => GameStateHelper::getBoardState(),
            'score' => GameStateHelper::getWins(),
            'currentTurn' => GameStateHelper::getCurrentTurn(),
            'victory' => $winner,
            'finished' => boolval($winner),
        ];
    }

    /**
     * Get current board state and return it from model
     * formatted for returning in response
     *
     * @return array
     */
    public static function getBoardState(): array {
        $boardSquares = BoardSquare::all()->sortBy(['y_cord']);

        $formattedSquares = [];
        foreach ($boardSquares as $square) {

            $formattedSquares[$square->y_cord][$square->x_cord] = (is_null($square->square_value) ? self::EMPTY_PIECE : $square->square_value);
        }

        return $formattedSquares;
    }

    /**
     * Get wins for each of the game symbols
     *
     * @return array
     */
    public static function getWins(): array {
        $wins = DB::table("wins")->select("board_symbol", "wins_count")->get();

        $formattedWins = [];
        foreach ($wins as $item) {
            $formattedWins[$item->board_symbol] = $item->wins_count;
        }

        return $formattedWins;
    }

    /**
     * Returns whose turn it is (x or o)
     *
     * @return string x or o
     */
    public static function getCurrentTurn(): string {
        $lastMadeTurn = DB::table('turns')->orderByDesc("id")->first();

        /** If there is no last turn in DB, that means its first turn right now - return x as in classic ttc, the x starts the game */
        if (!$lastMadeTurn)
            return self::X_PIECE;

        switch ($lastMadeTurn->turn_symbol) {
            case self::X_PIECE:
                return self::O_PIECE;
            case self::O_PIECE:
                return self::X_PIECE;
            default:
                return self::X_PIECE;
        }
    }

    /**
     * Check if there is a game piece on given coordinates on board
     *
     * @param integer $x
     * @param integer $y
     * @return string|null
     */
    public static function checkPieceOnCoords(int $x, int $y) {
        return BoardSquare::where([
            'x_cord' => $x,
            'y_cord' => $y
        ])->first()->square_value;
    }

    /**
     * Add record to DB with made turn, allowing other piece to make a new turn
     */
    public static function swapTurn(string $piece, int $square_id) {
        DB::table('turns')->insert([
            'turn_symbol'   =>  $piece,
            'square_id'     =>  $square_id,
            'created_at'    =>  Carbon::now()->toDateTimeString(),
            'updated_at'    =>  Carbon::now()->toDateTimeString()
        ]);
    }

    /**
     * Check if there is a winner after checking winning lines and if there is
     * return winning piece or if there is no winner, return null
     *
     * @return string|null
     */
    public static function checkIfThereIsWinner() {
        $winningLines = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6],
        ];

        $squares = BoardSquare::all()->sortBy(['y_cord']);

        for ($i = 0; $i < count($winningLines); $i++) {
            [$a, $b, $c] = $winningLines[$i];

            if (
                $squares[$a]->square_value &&
                $squares[$a]->square_value == $squares[$b]->square_value &&
                $squares[$a]->square_value === $squares[$c]->square_value
            ) {
                return $squares[$a]->square_value;
            }
        }

        return "";
    }

    /**
     * Update necessary records in DB after winning move
     * - We don't clear saved turns - giving next turn to the player who lost :)
     *
     * @param string $winningPiece
     */
    public static function updateGameStateAfterWin(string $winningPiece) {
        DB::table("wins")->where("board_symbol", $winningPiece)->increment("wins_count");
    }
}
