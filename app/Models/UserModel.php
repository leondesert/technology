<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users'; // Имя таблицы базы данных, связанной с моделью
    protected $primaryKey = 'user_id'; // Первичный ключ таблицы
    protected $allowedFields = ['user_login', 'user_pass', 'filter', 'role', 'agency_id', 'opr_id', 'stamp_id', 'tap_id', 'share_id', 'pre_share_id', 'parent', 'tables_states', 'colums_position', 'user_mail', 'user_phone','user_desc', 'user_photo_url', 'start_date', 'end_date', 'secret_key', 'fio', 'acquiring', 'is_airline' ]; // Поля, доступные для работы с таблицей
}
