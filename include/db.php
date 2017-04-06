<?php
ini_set('session.gc_maxlifetime', 2629743);
ini_set('session.cookie_lifetime', 2629743);
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/sessions');
session_start();

$link = mysqli_connect("localhost", "bestshoots", "bestshoots", "bestshoots");
mysqli_query($link, "SET NAMES 'utf8'");

// Отслеживаем ошибки при соединении
if( !$link ){
	echo 'Ошибка: '
		. mysqli_connect_errno()
		. ':' 
		. mysqli_connect_error();
}

//email
$config['from_mail'] = 'sales@codejs.pro';

function ordinal_suffix($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num .'st';
            case 2: return $num .'nd';
            case 3: return $num .'rd';
        }
    }
    return $num .'th';
}

//первая буква заглавная
function mb_strtoupper_first($str, $encoding = 'UTF8') {
	return
		mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
		mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
}
