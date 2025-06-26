<?php

namespace App\Controllers;

class UtilsController extends BaseController
{

    //функция для округления
    public static function rounding($number)
    {
        return round($number, 2);
    }

}
