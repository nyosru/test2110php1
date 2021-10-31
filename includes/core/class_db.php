<?php

class DB
{

    private static $db;

    public static function connect()
    {
        if (!self::$db) {
            try {
                self::$db = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';charset=utf8mb4;', DB_USER, DB_PASS, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
            } catch (PDOException $e) {
                die('Error!: ' . $e->getMessage() . '<br/>');
            }
        }
        return self::$db;
    }

    /**
     * запрос с защищёнными данными с помощью PDO
     */
    public static function query2($q, $data)
    {
        try {
            $sql = self::connect()->prepare($q);
            return $sql->execute($data);
        } catch (PDOException $e) {
            die('Error!: ' . $e->getMessage() . '<br/>');
        }
    }

    public static function query($q)
    {
        return self::connect()->query($q);
    }

    public static function fetch_row($q)
    {
        return $q->fetch();
    }

    public static function fetch_all($q)
    {
        return $q->fetchAll();
    }

    public static function insert_id()
    {
        return self::connect()->lastInsertId();
    }

    public static function error()
    {
        $res = self::connect()->errorInfo();
        trigger_error($res[2], E_USER_WARNING);
        return $res[2];
    }
}
