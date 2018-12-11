<?php
header('Content-Type: application/json');

$fn = '../bulletin_board_content.dat';

if(!file_exists($fn))
    die(json_encode(['error' => 'File not found!']));

if(isset($_REQUEST['t']))
    die(json_encode(
        ['mod_time' => filemtime('../bulletin_board_content.dat')]
    ));

$file=fopen($fn, "r");
flock($file, LOCK_SH);
$messages = file_get_contents($fn);
flock($file, LOCK_UN);
fclose($file);

$result = array();

$elements = array_filter(explode('<dt>', $messages));

foreach ($elements as $element){
    $message = array();
    $user_and_message = array_filter(explode('<strong>', $element));

    preg_match('#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i', $user_and_message[0], $image);

    if(!empty($image))
        $message['user_image'] = $image[0];

    array_shift($user_and_message);

    $user_and_message = explode('sagt:</strong>', $user_and_message[0]);
    $message['user'] = trim($user_and_message[0]);
    $message['message'] = trim(str_replace(array('</dt>', '<dd>', '</dd>'), array('', '', ''), $user_and_message[1]));

    $result[] = $message;
}

echo json_encode(array_reverse ($result));
