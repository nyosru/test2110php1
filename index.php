<?php

// INIT

require('./cfg/general.inc.php');
require('./includes/core/functions.php');

spl_autoload_register(function ($class_name) {
    require './includes/core/class_' . strtolower($class_name) . '.php';
});

$includes_dir = opendir('./includes/controllers_common');
while (($inc_file = readdir($includes_dir)) != false)
    if (strstr($inc_file, '.php')) require('./includes/controllers_common/' . $inc_file);

// GENERAL

Session::init();
Route::init();

$g['path'] = Route::$path;
$g['year'] = date('Y');

// в PDO можно и нужно использовать защиту вставки непонятного через переменные, 
// путём использования обработки переменных с помощью PDO (наприер id который проверяем цифра это или нет .. что излишне) это позволит 
// не использовать явных доп проверок и ускорит процесс

// API response 
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($g['path'] == 'user.get') {
        header('Content-type:application/json;charset=utf-8'); 
        die(json_encode(User::owner_info($_REQUEST)));
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($g['path'] == 'user.update') {
        header('Content-type:application/json;charset=utf-8'); 
        die(json_encode(User::owner_update($_REQUEST)));
    }
}

// OUTPUT

HTML::assign('global', $g);
HTML::display('./partials/index.html');
