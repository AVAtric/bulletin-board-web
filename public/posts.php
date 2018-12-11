<?php
// Send header
header('Content-Type: application/json');

// Init globals
$GLOBALS['error'] = "";
$GLOBALS['result'] = array();

// Init variables
$result= array();
$result['mod_time'] = null;
$fn = '../bulletin_board_content.dat';

// Some helper functions
function save_error(){
    $GLOBALS['result']['error'] = $GLOBALS['error'];
}

function detect_errors($request){
    if(empty($request)){
        return true;
    }

    save_error();
    return false;
}

function empty_error()
{
    save_error();

    if ($GLOBALS['error'] == "")
        return true;

    return false;
}

function exit_return()
{
    return json_encode($GLOBALS['result']);
}

// Here the main process begins

// Check if file exists
if(!file_exists($fn))
    $GLOBALS['error'] = 'File not found!';

if(!empty_error())
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

// Try to parse data
$elements = array_filter(explode('<dt>', $messages));

// Look into each element of file an validate and save
foreach ($elements as $element){
    $message = array();
    $user_and_message = array_filter(explode('<strong>', $element));

    preg_match('#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i', $user_and_message[0], $image);

    if(!empty($image))
        $message['user_image'] = $image[0];

    if(count($user_and_message) < 2)
        $GLOBALS['error'] = 'File corrupted!';

    if(!empty_error())
        exit(exit_return());

    array_shift($user_and_message);

    $user_and_message = explode('sagt:</strong>', $user_and_message[0]);
    $message['user'] = trim($user_and_message[0]);
    $message['message'] = trim(str_replace(array('</dt>', '<dd>', '</dd>'), array('', '', ''), $user_and_message[1]));

    if (($message['user'] == "") || ($message['message'] == "")){
        $GLOBALS['error'] = 'File corrupted!';
        exit(exit_return());
    }

    $GLOBALS['result']['messages'] = $message;
}

// If everything went fine output the result
echo json_encode(array_reverse ($result));
