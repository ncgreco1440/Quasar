<?php
namespace Quasar;
class Kernel
{
    public static function getQuery()
    {
        return $_SERVER['QUERY_STRING'];
    }

    public static function getRequest()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getServerIP()
    {
        return $_SERVER['SERVER_ADDR'];
    }

    public static function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    public static function getServerSoftware()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    public static function getServerProtocol()
    {
        return $_SERVER['SERVER_PROTOCOL'];
    }

    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getPost()
    {
        return $_POST;
    }
}