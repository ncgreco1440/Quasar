<?php
namespace Functions;

use Quasar\Kernel;

class Functions
{
    public static function helloWorld()
    {
        echo "Hello, World!<br/>";
    }

    public static function yieldCopyright()
    {
        $str = "&copy; Copyright 2016";
        $str .= date('Y') > 2016 ? " - " .date('Y') : "";
        return $str;
    }

    public static function prepareView()
    {
        $request = Kernel::getRequest();
        switch($request)
        {
            case "/": {
                return "home";
            }
            case "/about": {
                return "about";
            }
            case "/contact": {
                return "contact";
            }
        }
    }
}