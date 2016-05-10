<?php
namespace Quasar\Users;

use Authentication\Authenticate;
use Authentication\Validate;
use Database\Connection;

class Profile
{
    public static function saveProfile($firstname, $lastname, $email)
    {
        if($token = Validate::validateToken())
        {
            $conn = Connection::getConnection();
            $tablename = "Q_USERS";
            $fields = Connection::mysqlClean(compact('firstname', 'lastname', 'email'));
            if(Connection::encryptAndStore(compact('tablename', 'fields', 'token')))
                return ["success" => true, "message" => "User Profile Saved"];
            else
                return ["success" => false, "message" => "User Profile Save Error"];
        }
        else
            return ["success" => false, "message" => "Invalid Token"];
    }
}