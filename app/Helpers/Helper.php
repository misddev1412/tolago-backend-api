<?php
namespace App\Helpers;
use Illuminate\Http\Request;

class Helper {
    public static function getClientIps()
    {
        return $_SERVER['REMOTE_ADDR'];
    } 

    public static function getClientAgent() {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}