<?php

namespace App\Controllers;

class UtilsController extends BaseController
{

    public static function rounding($number)
    {   
        // Округляем число до 2 знаков после запятой и форматируем его как строку
        // с двумя знаками после запятой, используя точку в качестве десятичного разделителя.
        return number_format((float)round($number, 2), 2, '.', '');
    }

}
