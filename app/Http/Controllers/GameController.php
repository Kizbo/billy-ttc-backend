<?php

namespace App\Http\Controllers;

use App\Helpers\GameStateHelper;
use Illuminate\Http\Request;
use App\Models\BoardSquare;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GameController extends Controller {


    /**
     * Display a informations about the game
     *
     * @return \Illuminate\Http\Response
     */
    public function game() {
        return response()->json(GameStateHelper::getGameState());
    }



    /**
     * Update Square with given symbol, on given coordinates
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $piece - x or o
     * @return \Illuminate\Http\Response
     */
    public function update_board_square(Request $request, string $piece) {

        /**
         * If there was already a winner after last turn, do nothing and just return game state
         */
        $winner = GameStateHelper::checkIfThereIsWinner();
        if ($winner)
            return response()->json(GameStateHelper::getGameState());

        /**
         * If given game piece is invalid (not a x or o), return error
         */
        if (!in_array(strtolower($piece), [GameStateHelper::X_PIECE, GameStateHelper::O_PIECE]))
            return response()->json([
                'code'  =>  400,
                'errors' =>  "Invalid game piece provided in request URL"
            ], 400);


        /**
         * Verify if user gave correct coordinates on board and
         * if not, return an error
         */
        $coordinatesRules = [
            'required',
            'integer',
            "max:2",
            "min:0"
        ];
        $validator = Validator::make($request->all(), [
            'x' =>  $coordinatesRules,
            'y' =>  $coordinatesRules
        ]);

        if ($validator->fails())
            return response()->json([
                'code'      =>  400,
                'errors'    =>  $validator->errors(),
            ], 400);


        /**
         * The input is correct, now we need to check
         * - if piece is not placed out of turn
         * - if piece is not placed in place where another piece already is
         */

        if (GameStateHelper::getCurrentTurn() !== $piece)
            return response()->json(null, 406);

        $coords = $request->only(['x', 'y']);
        if (GameStateHelper::checkPieceOnCoords($coords['x'], $coords['y']) !== null)
            return response()->json(null, 409);


        /**
         * All is alright, change board square with given value
         */
        $squareToUpdate = BoardSquare::where([
            'x_cord' => $coords['x'],
            'y_cord' => $coords['y']
        ])->first();
        $squareToUpdate->square_value = $piece;
        $squareToUpdate->save();


        /**
         * Then swap turn to other piece, updating turns table with last turn
         */

        GameStateHelper::swapTurn($piece, $squareToUpdate->id);


        /**
         * Game won or not, return game state to the user
         */
        return response()->json(GameStateHelper::getGameState());
    }

    /**
     * Restart the game and update win scores if there was a winner in last round
     *
     * @return \Illuminate\Http\Response
     */
    public function restart_game() {
        $winner = GameStateHelper::checkIfThereIsWinner();
        if ($winner)
            GameStateHelper::updateGameStateAfterWin($winner);

        BoardSquare::query()->update(['square_value' => null]);

        return response()->json(GameStateHelper::getGameState());
    }

    /**
     * Reset game, removing scores and resetting everything to default values
     *
     * @return \Illuminate\Http\Response
     */
    public function reset_game() {
        BoardSquare::query()->update(['square_value' => null]);
        DB::table("wins")->update(["wins_count" => 0]);
        DB::table("turns")->truncate();

        return response()->json([
            'currentTurn' => GameStateHelper::getCurrentTurn()
        ]);
    }
}
