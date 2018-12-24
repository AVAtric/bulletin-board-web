<?php
// Send header
header('Content-Type: application/json');

// Init globals
$GLOBALS['error'] = "";
$GLOBALS['warning'] = "";
$GLOBALS['result'] = array();

// Init variables
$result= array();
$result['mod_time'] = null;
$fn = '../bulletin_board_content.dat';

// Some helper functions
function _has_error(){
    if($GLOBALS['error'] != "")
        return true;

    return false;
}

function _has_warning(){
    if($GLOBALS['warning'] != "")
        return true;

    return false;
}

function _save_problem(){
    if(_has_error())
        $GLOBALS['result']['error'] = $GLOBALS['error'];

    if(_has_warning())
        $GLOBALS['result']['warning'] = $GLOBALS['warning'];
}

function _has_problem(){
    if(_has_error() || _has_warning())
        return true;

    return false;
}

function empty_problem(){
    if(_has_problem()){
        _save_problem();
        return false;
    }

    return true;
}

function exit_return(){
    if(_has_problem())
        _save_problem();

    return json_encode($GLOBALS['result']);
}

/*
 * Here the main process begins
 */

// Check if file exists
if(!file_exists($fn))
    $GLOBALS['error'] = 'File not found!';

if(!empty_problem())
    exit(exit_return());

// Save modification time of file
$GLOBALS['result']['mod_time'] = filemtime($fn);

// Finish application if only time is requested
if(isset($_REQUEST['t'])){
    exit(exit_return());
}

// Open, lock and read file
$file=fopen($fn, "r");
flock($file, LOCK_SH);
$messages = file_get_contents($fn);
flock($file, LOCK_UN);
fclose($file);

// Check if there are any messages
if(trim($messages) == "")
    $GLOBALS['warning'] = 'No messages found!';

if(!empty_problem())
    exit(exit_return());

// Try to parse data
$elements = array_filter(explode('<dt>', $messages));

// Look into each element of file to validate and save
foreach ($elements as $element){
    $message = array();
    $user_and_message = array_filter(explode('<strong>', $element));

    preg_match(
        '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i',
        $user_and_message[0],
        $image
    );

    if(!empty($image))
        $message['user_image'] = str_replace('"', '', $image[0]);

    if(count($user_and_message) < 2)
        $GLOBALS['error'] = 'File corrupted!';

    if(!empty_problem())
        exit(exit_return());

    array_shift($user_and_message);

    $user_and_message = explode('sagt:</strong>', $user_and_message[0]);
    $message['user'] = trim($user_and_message[0]);
    $message['message'] = trim(str_replace(array('</dt>', '<dd>', '</dd>'), array('', '', ''), $user_and_message[1]));

    if (($message['user'] == "") || ($message['message'] == "")){
        $GLOBALS['error'] = 'File corrupted!';
        exit(exit_return());
    }

    $GLOBALS['result']['messages'][] = $message;
}

$GLOBALS['result']['messages'] = array_reverse($GLOBALS['result']['messages']);

// If everything went fine output the result
echo exit_return();
