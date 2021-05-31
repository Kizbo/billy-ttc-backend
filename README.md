# Tic tac toe Laravel version - Billy Tech Task

A simple game of tic tac toe, based on Laravel. All communication is
based on API routes. 

## How to run it

To run this app:


- clone repository
- create your own .env file based on env.example and set access to DB, then:
```sh
composer install
php artisan migrate
php artisan serve
```
To deploy this app to production, see:
https://laravel.com/docs/8.x/deployment

## Usage

All the routes for the game are defined in the routes/api.php. I removed prefix from the API routes to match requirements for the task. They are all handled by GameController and most of the business logic is inside Helpers/GameStateHelper. 

Board squares have their model defined inside Models/BoardSquare for convenience.

### Routes

Method | Route | Description | Request Body
------ | ----- | ----------- | ------------
GET | / | Retrieve game state and round status
POST | /:piece | Place a piece (x or o) on given coordinates if it is possible. Coordinates should be in body of the request. Retrieves game state after placement in the body of response | ```x: number, y: number ```
POST | /restart | Restart game current game and add to score of the winning player, if there was a winner in last round. Returns game state after restart |
DELETE | /  | Reset the game, resetting scores and board and returns current turn




