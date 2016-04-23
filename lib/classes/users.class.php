<?php
namespace Quasar;

use Database\Connection;
class Users
{
    public static function getUser($name)
    {
        $select = ["username", "email", "lastname", "firstname"];
        $from = "LEV_users";
        $where = ["username" => $name];
        return Connection::decryptAndShow(compact("select", "from", "where"));
    }
}