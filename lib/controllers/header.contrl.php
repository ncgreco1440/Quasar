<?php
namespace Quasar\Application;

use Database\Connection;

class Header
{
    public static function getMainNavigation()
    {
        $db = Connection::getConnection();
        $select = "*";
        $from = "main_navigation";
        $header = Connection::simplySelAll(compact("select", "from"));
        foreach($header as $key => $value)
        {
            $tmp = strtolower($value['name']);
            $tmp = str_replace(" ", "-", $tmp);
            $value['name'] == "Home" ? $value['link'] = "/" : $value['link'] = $tmp;
            $header[$key] = $value;
        }
        return $header;
    }
};
