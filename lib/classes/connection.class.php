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
        // SELECT
        $query = "SELECT ";
        $query .= self::assembleQuery_SELECT_ENCRYPTED($data['select']);
        $query .= self::assembleQuery_SELECT_UNENCRYPTED($data['select']);
        // FROM
        $query .= self::assembleQuery_FROM($data['from']);
        // WHERE
        if(isset($data['where']))
            $query .= self::assembleQuery_WHERE($data['where']);
        // ORDER BY
        if(isset($data['orderby']))
            $query .= self::assembleQuery_ORDERBY($data['orderby']);
        // LIMIT
        if(isset($data['limit']))
            $query .= self::assembleQuery_LIMIT($data['limit']);
        $data = self::executeQuery($query);
        $data = self::fetchAssoc($data);
        if(count($data))
            return $data;
        else
            return [];
    }

/* =================================================================================================
        PRIVATE METHODS
================================================================================================= */

    private static function assembleQuery_SELECT_ENCRYPTED($data)
    {
        $select = "";
        $i = 1;
        foreach($data['encrypted'] as $key => $value)
        {
            $select .= "AES_DECRYPT(`$value`, 'Grasshopper') as `$value`";
            if($i != count($data['encrypted']))
                $select .= ", ";
            else
                if(count($data['unencrypted']))
                    $select .= ", ";
                else
                    $select .= " ";
            $i++;
        }
        return $select;
    }

    private static function assembleQuery_SELECT_UNENCRYPTED($data)
    {
        $select = "";
        $i = 1;
        foreach($data['unencrypted'] as $key => $value)
        {
            $select .= "`$value`";
            if($i != count($data['unencrypted']))
                $select .= ", ";
            else
                $select .= " ";
            $i++;
        }
        return $select;
    }

    private static function assembleQuery_FROM($data)
    {
        return "FROM `$data` ";
    }

    private static function assembleQuery_WHERE($data)
    {
        $i = 1;
        $where = "WHERE ";
        foreach($data as $key => $value)
        {
            $where .= "AES_DECRYPT(`$key`, 'Grasshopper') = '$value'";
            if($i != count($data))
                $where .= "AND ";
            $i++;
        }
        return $where;
    }

    private static function fetchAssoc($data)
    {
        return $data->fetch_assoc();
    }
}

