<?php

class User
{

    // GENERAL

    public static function user_info($data)
    {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $phone = isset($data['phone']) ? preg_replace('~[^\d]+~', '', $data['phone']) : 0;
        // where
        if ($user_id) $where = "user_id='" . $user_id . "'";
        else if ($phone) $where = "phone='" . $phone . "'";
        else return [];
        // info
        $q = DB::query("SELECT user_id, first_name, last_name, middle_name, email, gender_id, count_notifications FROM users WHERE " . $where . " LIMIT 1;") or die(DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'gender_id' => (int) $row['gender_id'],
                'email' => $row['email'],
                'phone' => (int) $row['phone'],
                'phone_str' => phone_formatting($row['phone']),
                'count_notifications' => (int) $row['count_notifications']
            ];
        } else {
            return [
                'id' => 0,
                'first_name' => '',
                'last_name' => '',
                'middle_name' => '',
                'gender_id' => 0,
                'email' => '',
                'phone' => '',
                'phone_str' => '',
                'count_notifications' => 0
            ];
        }
    }

    public static function user_get_or_create($phone)
    {
        // validate
        $user = User::user_info(['phone' => $phone]);
        $user_id = $user['id'];
        // create
        if (!$user_id) {
            DB::query("INSERT INTO users (status_access, phone, created) VALUES ('3', '" . $phone . "', '" . Session::$ts . "');") or die(DB::error());
            $user_id = DB::insert_id();
        }
        // output
        return $user_id;
    }



    public static function user_update($data)
    {

        // сначала выявим ошибки
        if (
            empty($data['id']) 
            || empty($data['first_name']) 
            || empty($data['last_name']) 
            || empty($data['phone']) 
            || !isset($data['middle_name']) 
            || !isset($data['email']) 
            ) {
            // можно запускать исключение и его отлавливать/отслеживать там где мы вызываем этот метод
            //  throw new Exception("Error Processing Request", 1);                        
            return ['status' => 'error', 'error_text' => 'не все поля заполнены'];
        }

        // если ошибок нет то уже обрабатываем велидируем и отправляем запрос

        DB::add_pole_to_update( 'phone', $data['phone'] );
        if ( empty(DB::$updateVar[':phone']) ) return ['status' => 'error', 'error_text' => 'телефон указан не верно ( пример +7(999)888-77-66 )'];

        DB::add_pole_to_update('first_name', $data['first_name']);
        DB::add_pole_to_update('last_name', $data['last_name']);
        // могут быть нулём
        DB::add_pole_to_update('middle_name', $data['middle_name'] ?? null );
        DB::add_pole_to_update('email', $data['email'] ?? null );

        $result = DB::query_update( 'user_id', $data['id'] );

        // output
        // return $user_id;
        return [
            'status' => 'ok',
            'result_sql' => $result,
            // 'sql_query' => $sql_query,
            // 'sql_data' => $inSqlVars
        ];
    }

    // TEST

    public static function owner_info($requery)
    {
        // your code here ...
        // добавил входящие параметры .. так как без них всегда будет пустой ответ (массив)
        return self::user_info($requery);
    }

    // если дата пустая то на самом старте пусть будет ошибка, которую можно отловить try{} catch{} выше
    // public static function owner_update($data = [])
    public static function owner_update($data)
    {
        // your code here ...
        $result = self::user_update($data);
        return $result;
        // return ['status' => 'ok'];
    }
}
