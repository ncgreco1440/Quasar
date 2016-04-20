<?php
namespace Functions;
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
}