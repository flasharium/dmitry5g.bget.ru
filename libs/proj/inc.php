<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once "db.php";
require_once "templates.php";


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
function array_get($array, $path) {
    $result = $array;
    $parts = explode('.', $path);
    foreach ($parts as $part) {
        $result = is_array($result) && isset($result[$part]) ? $result[$part] : array();
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
