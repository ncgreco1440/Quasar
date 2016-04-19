<?php 
namespace Database;
/** ====================================================================================

    1. 

    Namespaces are extremely useful when you have a bunch of classes that 
    that relate to one another in some way, but don't necessarily 
    inherit from one another. It's extremely useful for cutting
    done on amount of files and folders you need.

====================================================================================*/
class Connection
{
    private static $_connection = false;

    public static function connect($h, $n, $u, $p) 
    {
        if(!$_connection = new \mysqli($h, $u, $p, $n))
            echo "Connection Error!<br/>";
        else
            echo "Database Obtained A Successful Connection!<br/>";
    }
}

class Extra
{
    public static function invoked()
    {
        echo "Method Invoked From Extra class in Database namespace<br/>";
    }
}