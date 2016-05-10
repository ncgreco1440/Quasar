<?php
namespace Quasar;

use Database\Connection;
class Users
{
    public static function getUser($name)
    {
        $select = ["encrypted" => ["username", "email", "lastname", "firstname"],
                    "unencrypted" => ["permissionID", "active", "lastsignin",
                    "Q_USER_PERMISSION_LVLS`.`title"]];
        $from = "Q_USERS";
        $join = "`Q_USER_PERMISSION_LVLS` ON `Q_USER_PERMISSION_LVLS`.`ID` = `permissionID`";
        $where = ["encrypted" => ["username" => $name]];
        return Connection::decryptAndShow(compact("select", "from", "join",
            "where"));
    }

    public static function getUsers()
    {
        $select = ["encrypted" => ["username"]];
        $from = "Q_USERS";
        return Connection::decryptAndShow(compact("select", "from"));
    }
}