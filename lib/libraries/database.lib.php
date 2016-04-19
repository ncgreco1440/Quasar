<?php 
namespace Database;

echo "Library loaded";
class Database
{
    private static $_connection = false;
    private static $_host;
    private static $_pass;
    private static $_name;
    private static $_user;

    public static function connect($h, $n, $u, $p) 
    {
        echo "invoked";
        $_connection = new PDO("mysql: host=$h; dbname=$n", $u, $p);
    }

    public static function live()
    {
        if($_connection)
            echo "Connection Established";
        else
            echo "Connection Inactive";
    }
}