<?php
//I, Xuan Huy Pham, 000899551, certify that this material is my original work. 
//No other person's work has been used without suitable acknowledgment, and I have not made my work available to anyone else.

/**
 * @author Xuan Huy Pham
 * @version 20231202.00
 * @package COMP 10260 Assignment 3
 * 
 */

session_start();

/**
 * this function help determine the optiomal move 
 */
function determineOptimalMove($stones)
{
    $remainder = $stones % 4;

    //pick a move base on the remainder value
    switch ($remainder) {
        case 3:
            return 2;
        case 2:
            return 1;
        case 1:
            return rand(1, 3);//a random number 1-3 will be selected
        case 0:
            return 3;
    }
}

/**
 * this function is to initialize the game, to reset or play
 */
function initializeGame() {
    //set the initial number of stones
    $_SESSION['stones'] = 20;
    //set the current player to starting player
    $_SESSION['player'] = "player";
    //set the initial game state
    $_SESSION['winner'] = "game is started";
    //set the computer's move at 0 at the beginning
    $_SESSION['computerMove'] = 0;
}

//check if the game has been already initialized
if (!isset($_SESSION['initialized'])) {
    initializeGame();
    $_SESSION['initialized'] = true;//mark it as initialized
}


//initialize vars by retrieving value from GET
$mode = isset($_GET['mode']) ? intval($_GET['mode']) : 0;
$difficulty = isset($_GET['difficulty']) ? intval($_GET['difficulty']) : 0;
$playerMove = isset($_GET['player_move']) ? intval($_GET['player_move']) : 0;

//check to reset the game
if ($mode === 0) {
    initializeGame();
} else {
    //player's turn
    if ($_SESSION['stones'] > 0) {
        $_SESSION['player'] = "player";

        //update stones after player's move
        $_SESSION['stones'] -= min($playerMove, $_SESSION['stones']);

        //determine the computer's move based on difficulty
        if ($_SESSION['stones'] > 0) {
            if ($difficulty === 0) { //random guess
                $_SESSION['computerMove'] = rand(1, 3);
            } else { //optimal play
                $_SESSION['computerMove'] = determineOptimalMove($_SESSION['stones']);
            }

            //update stones after computer's move
            $_SESSION['stones'] -= $_SESSION['computerMove'];
        }

        //check if the game is over
        if ($_SESSION['stones'] <= 0) {
            //determine the winner based on the current player
            $_SESSION['winner'] = ($_SESSION['player'] === "player") ? "Computer wins!" : "Player wins!";
        } else {//switch from player to computer for the next turn
            $_SESSION['player'] = ($_SESSION['player'] === "player") ? "computer" : "player";
        }
    }
}

//output the result as a json-encoded array
$response = array(
    "move" => $mode === 0 ? 0 : $_SESSION['computerMove'],
    "stones" => $_SESSION['stones'],
    "player" => $_SESSION['player'],
    "winner" => $_SESSION['winner']
);
echo json_encode($response);
?>
