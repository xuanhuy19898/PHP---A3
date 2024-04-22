<?php
//I, Xuan Huy Pham, 000899551, certify that this material is my original work. 
//No other person's work has been used without suitable acknowledgment, and I have not made my work available to anyone else.

/**
 * @author Xuan Huy Pham
 * @version 20231202.00
 * @package COMP 10260 Assignment 3
 */

session_start();

//handle AJAX GET request
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header("Content-Type: application/json");
    $mode = isset($_GET["mode"]) ? $_GET["mode"] : "";
    $letter = isset($_GET["letter"]) ? $_GET["letter"] : "";

    //initialize or reset game
    if ($mode === "reset") {
        $gameState = startGame();
    } else {
        //update game state based on user input
        $gameState = updateGameState($_SESSION["hangman"] ?? startGame(), $letter);
    }
    //save game state in session
    $_SESSION["hangman"] = $gameState;
    echo json_encode($gameState);//return json-encoded game state to the client
}

/**
 * read a random word from wordlist.txt
 * @return string return a randomly selected word from the text file
 */
function getWord() {
    $wordlist = file("wordlist.txt", FILE_IGNORE_NEW_LINES);
    return $wordlist[array_rand($wordlist)];
}

/**
 * initialize the game
 */
function startGame() {
    $secretWord = getWord();
    $initialSecret = preg_replace('/[a-z]/i', '-', $secretWord);
    return [
        "secret" => $initialSecret,
        "actualSecret" => $secretWord,
        "guesses" => "",
        "alphabet" => "abcdefghijklmnopqrstuvwxyz",
        "strikes" => 0,
        "status" => "new game started!",
    ];
}

/**
 * update the game state based on user's input
 * @return array return an array representing the current game state
 */
function updateGameState($gameState, $letter) {
    $letter = strtolower($letter);

    //check if the letter has already been guessed, nothing will happen if the letter is already selected
    if (strpos($gameState["guesses"], $letter) !== false) {
        return $gameState;
    }

    //update the guesses
    $gameState["guesses"] .= $letter;
    $secretWord = strtolower($gameState["actualSecret"]);
    $guessedLetter = strtolower($letter);

    //check if the selected letter is in the actual secret word
    $pos = strpos($secretWord, $guessedLetter);
    if ($pos !== false) {
        //update the secret word with the correct guessed letter
        $gameState["secret"] = substr_replace($gameState["secret"], $letter, $pos, 1);
        $gameState["status"] = "You are playing the game.";
    } else {
        //incorrect guess will increment the num of strikes
        $gameState["strikes"]++;

        //check for game state, it's over and user loses if there are more than 7 incorrect guesses
        if ($gameState["strikes"] >= 7) {
            $gameState["status"] = "You lost.";
            $gameState["secret"] = $gameState["actualSecret"];
        } else {
            $gameState["status"] = "You are playing the game.";
        }
    }

    //update the list of remaining available letters
    $gameState["alphabet"] = str_replace($guessedLetter, "", $gameState["alphabet"]);

    //win the game if user have all correct guesses with less than 7 guesses
    if (strpos($gameState["secret"], "-") === false && $gameState["strikes"] < 7) {
        $gameState["status"] = "You won!";
    }
    return $gameState;
}
?>
