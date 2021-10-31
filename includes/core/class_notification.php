<?php

class Notification
{

    // TEST

    // your code here ...

    public static function get($unread = null)
    {
        $sql = DB::query('SELECT `title`, `description`, '
            // можно в базе формировать нужные поля исходя из текущих данных
            // .' CASE `viewed` WHEN TRUE THEN \'yes\' ELSE \'no\' END as `readed` , '
            . ' `viewed` ,  `created` FROM `user_notifications` ' . (!empty($unread) ? ' WHERE viewed = FALSE ' : '')) or die(DB::error());
        return DB::fetch_all($sql);
    }

    // отмечаем все уведомления прочитанными
    public static function set_readed()
    {
        return DB::query('UPDATE `user_notifications` SET `viewed` = TRUE ') or die(DB::error());
    }
}
