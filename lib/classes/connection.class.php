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
        self::$_connection = new \mysqli($db['DB_HOST'], $db['DB_USERNAME'], $db['DB_PASSWORD'], $db['DB_DATABASE']);
        if(self::$_connection->connect_errno > 0)
            die("Could not establish a connection to the database!");
    }

    public static function getConnection()
    {
        return self::$_connection;
    }

    public static function mysqlClean($array)
    {
        $return = [];
        foreach($array as $key => $value)
            $return[$key] = mysqli_real_escape_string(self::$_connection, $value);
        return $return;
    }

    public static function executeQuery($query)
    {
        return self::$_connection->query($query);
    }

    /**
     * [encryptAndStore]
     *
     * Encrypts sensitive data for storage within the database, and proceeds to
     * store it.
     *
     * @param  [array] $data   [data with info on table, and contents to be encrypted]
     * @param  [string] $token [user generated token for this session]
     * @return [bool]          [return successful or not]
     */
    public static function encryptAndStore($data)
    {
        $i = 1;
        $query = "UPDATE `$data[tablename]` SET ";
        foreach($data['fields'] as $key => $value)
        {
            $query .= "`$key` = AES_ENCRYPT('$value', 'Grasshopper')";
            if($i != count($data['fields']))
                $query .= ", ";
            else
                $query .= " ";
            $i++;
        }
        //$query .= "WHERE `token` = '$token'";
        if(self::executeQuery($query))
            return true;
        else
            return false;
    }
    /**
     * [decryptAndShow]
     *
     * Descrypts sensitive data from the database and returns it to calling function
     * to then display the data elsewhere.
     *
     * @param  [array] $data   [data with info on table, and contents to be decrypted]
     * @param  [string] $token [user generated token for this session]
     * @return [array]         [returns decrypted data]
     */
    public static function decryptAndShow($data)
    {
        $i = 1;
        $query = self::assembleQuery_SELECT($data['select']);
        $query .= self::assembleQuery_FROM($data['from']);
        $data = self::executeQuery($query);
        return $data->fetch_assoc();
    }

/* =================================================================================================
        PRIVATE METHODS
================================================================================================= */

    private static function assembleQuery_SELECT($data)
    {
        $select = "SELECT ";
        foreach($data as $key => $value)
        {
            $query .= "AES_DECRYPT(`$value`, 'Grasshopper') as `$value`";
            if($i != count($data))
                $query .= ", ";
            else
                $query .= " ";
            $i++;
        }
        return $select;
    }

    private static function assembleQuery_FROM($data)
    {
        $from = "FROM `$data` ";
    }
}

