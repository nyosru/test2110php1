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
if ( Route::$method == 'GET') {
    if (Route::$path == 'user.get') {
        return_result_api(User::owner_info($_REQUEST));
    }
    // получаем уведомления ( возможен флаг show_unreaded = *** если есть то только непрочитанные )
    elseif (Route::$path == 'notifications.get') {
        return_result_api(Notification::get($_REQUEST['get_unread'] ?? ''));
    }
} else if ( Route::$method == 'POST') {
    if (Route::$path == 'user.update') {
        return_result_api(User::owner_update($_REQUEST));
    }
    elseif (Route::$path == 'notifications.read') {
        return_result_api(Notification::set_readed());
    }
}

// OUTPUT

HTML::assign('global', $g);
HTML::display('./partials/index.html');
