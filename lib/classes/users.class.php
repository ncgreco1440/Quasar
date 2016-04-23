<?php
namespace Quasar;

use Database\Connection;
class Users
{
    public static function getUser($name)
    {
        $select = ["encrypted" => ["username", "email", "lastname", "firstname"],
                    "unencrypted" => ["permissionID", "active", "lastsignin"]];
        $from = "LEV_users";
        $where = ["username" => $name];
        return Connection::decryptAndShow(compact("select", "from",
            "where"));
    }

    public static function getUsers()
    {
        $select = ["encrypted" => ["username"],
                    "unencrypted" => []];
        $from = "LEV_users";
        return Connection::decryptAndShow(compact("select", "from"));
    }
}