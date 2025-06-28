<?php

namespace App\Controllers;

class UtilsController extends BaseController
{

    public static function rounding($number)
    {   
        // Округляем число до 2 знаков после запятой
        return round($number, 2);
    }

    public static function rounding_four($number)
    {   
        // Округляем число до 4 знаков после запятой
        return round($number, 4);
    }

}
