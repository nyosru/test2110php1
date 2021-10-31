<?php

class DB
{

    private static $db;

    public static $updateQuery = '';
    public static $updateVar = [];

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
    public static function query_update($id_pole, $id_value)
    {
        // ключевой id
        self::$updateVar[':' . $id_pole] = $id_value;
        // запрос и выполнение
        $sql = self::connect()->prepare('UPDATE `users` SET ' . self::$updateQuery . ' WHERE `' . $id_pole . '` = :' . $id_pole);
        $result = $sql->execute(self::$updateVar);
        // трём данные
        self::newUpdate();
        // возвращаем результат обработки запроса
        return $result;
    }

    public static function newUpdate()
    {
        self::$updateQuery = '';
        self::$updateVar = [];
    }

    public static function add_pole_to_update($pole, $value)
    {

        // приводим данные к нужному виду
        if ($pole == 'email') $value = strtolower($value);
        else if ($pole == 'phone') $value = flt_phone_number($value);

        self::$updateQuery .= (!empty(self::$updateQuery) ? ',' : '') . ' `' . $pole . '` = :' . $pole . ' ';
        self::$updateVar[':' . $pole] = $value;

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
