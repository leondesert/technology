<?php

namespace App\Controllers;

class UtilsController extends BaseController
{

    public static function rounding($number)
    {   
        // Округляем число до 2 знаков после запятой
        return round($number, 2);
    }

}
