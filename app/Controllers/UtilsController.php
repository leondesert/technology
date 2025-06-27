<?php

namespace App\Controllers;

class UtilsController extends BaseController
{

    // функция для округления в большую сторону
    public static function rounding($number)
    {   
        return round($number, 2);
    }

}
