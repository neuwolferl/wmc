<?php

function anti_injection($input) {
    $pulito = strip_tags(addslashes(trim($input)));
    $pulito = str_replace("'", "\'", $pulito);
    $pulito = str_replace('"', '\"', $pulito);
    $pulito = str_replace(';', '\;', $pulito);
    $pulito = str_replace('--', '\--', $pulito);
    $pulito = str_replace('+', '\+', $pulito);
    $pulito = str_replace('(', '\(', $pulito);
    $pulito = str_replace(')', '\)', $pulito);
    $pulito = str_replace('=', '\=', $pulito);
    $pulito = str_replace('>', '\>', $pulito);
    $pulito = str_replace('<', '\<', $pulito);
    return $pulito;
}

function float_format($number) {

    $flag = true; //disattiva la formattazione
    if (!$flag)
        return $number . "";

    return number_format($number, 2, "/", " ");
}

function cleanforjs($array) {
    foreach ($array as $key => $value) {
        $partial = $value;
        $partial = str_ireplace(array("'", "\n"), array("\'", ""), $partial);
        $array[$key] = $partial;
    }
    return $array;
}

function toJSArray($array) {
    $string = "new Array(";
    foreach ($array as $item) {
        $string .= "'" . $item . "', ";
    }
    $string[strlen($string) - 2] = ")";
    return $string;
}
