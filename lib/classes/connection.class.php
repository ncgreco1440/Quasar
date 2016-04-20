<?php
namespace Database;

use Application\Env;
Env\Env::createEnviornment();
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

    public static function connect()
    {
        $db = Env\Env::fetchEnv();
        $_connection = new \mysqli($db['DB_HOST'], $db['DB_USERNAME'], $db['DB_PASSWORD'], $db['DB_DATABASE']);
        if($_connection->connect_erno > 0)
            echo "Connection Error! ";//. $_connection->connect_erno ."<br/>";
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