<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once "db.php";
require_once "templates.php";


function long_session_start() {
    $cookieLifetime = 365 * 24 * 60 * 60;
    session_set_cookie_params($cookieLifetime);
    session_start();
    setcookie(session_name(), session_id(), time() + $cookieLifetime, '/');
}
long_session_start();

function dump($val){
    echo '<pre>';
    print_r($val);
    echo  '</pre>';
}

function to_flat_array($array, $key_filed, $value_filed) {
    $res = array();
    foreach ($array as $values) {
        $res[$values[$key_filed]] = $values[$value_filed];
    }
    return $res;
}

function json_error($text) {
    echo json_encode(array('error' => $text));
    die();
}

function redirect($url) {
    header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
    die;
}

/**
 * @return mixed
 */
function array_get($array, $path, $default = array()) {
    $result = $array;
    $parts = explode('.', $path);
    foreach ($parts as $part) {
        $result = is_array($result) && isset($result[$part]) ? $result[$part] : $default;
    }
    return $result;
}

function int($s){return(int)preg_replace('/[^\-\d]*(\-?\d*).*/','$1',$s);}

function generate_random_password() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function array_hash($array, $field = 'id') {
    $res = array();
    foreach ($array as $item) {
        $res[$item[$field]] = $item;
    }
    return $res;
}
