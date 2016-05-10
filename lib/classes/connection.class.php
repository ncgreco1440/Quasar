<?php
namespace Database;

use Application\Env;
Env::createEnviornment();
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
        $db = Env::fetchEnv();

        self::$_connection = new \mysqli($db['DB_HOST'], $db['DB_USERNAME'], $db['DB_PASSWORD'], $db['DB_DATABASE']);
        if(self::$_connection->connect_errno > 0)
        {
            self::$_connection = new \mysqli($db['DB_HOST'], $db['DB_USERNAME'], $db['DB_PASSWORD']);
            self::install($db);
            header("Location: /");
        }
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
        $env = Env::fetchEnv();
        $i = 1;
        $query = "UPDATE `$data[tablename]` SET ";
        foreach($data['fields'] as $key => $value)
        {
            $query .= "`$key` = AES_ENCRYPT('$value', '$env[APP_ENCRYPT_KEY]')";
            if($i != count($data['fields']))
                $query .= ", ";
            else
                $query .= " ";
            $i++;
        }
        $query .= "WHERE `token` = '$data[token]'";

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
        if(isset($data['select']['encrypted']))
            $query .= self::assembleQuery_SELECT_ENCRYPTED($data['select']);
        if(isset($data['select']['unencrypted']))
            $query .= self::assembleQuery_SELECT_UNENCRYPTED($data['select']);
        // FROM
        $query .= self::assembleQuery_FROM($data['from']);
        if(isset($data['join']))
            $query .= "LEFT JOIN ".$data['join'];
        // WHERE
        if(isset($data['where']))
            if(isset($data['where']['encrypted']))
                $query .= self::assembleQuery_WHERE_ENCRYPTED($data['where']);
            if(isset($data['where']['unencrypted']))
                $query .= self::assembleQuery_WHERE_UNENCRYPTED($data['where']);
        // ORDER BY
        if(isset($data['orderby']))
            $query .= self::assembleQuery_ORDERBY($data['orderby']);
        // LIMIT
        if(isset($data['limit']))
            $query .= self::assembleQuery_LIMIT($data['limit']);

        //echo $query;

        $data = self::executeQuery($query);
        $data = self::fetchAssoc($data);

        if(count($data))
            return $data;
        else
            return [];
    }

    public static function simplySelAll($data)
    {
        $return = [];
        $result = self::$_connection->query("SELECT ".$data['select']." FROM ".$data['from']."");
        while($value = $result->fetch_assoc())
            array_push($return, $value);
        return $return;
    }

/* =================================================================================================
        PRIVATE METHODS
================================================================================================= */

    private static function install($db)
    {
        // Create the Database and connect to it.
        self::createDatabase($db);
        self::$_connection = new \mysqli($db['DB_HOST'], $db['DB_USERNAME'], $db['DB_PASSWORD'],
            $db['DB_DATABASE']);

        // Populate Database with Tables if they are not already in the database.
        self::createUsers($db);
        self::createUserPermissions($db);
        self::createPages($db);
        self::createPageContents($db);

        // Create Views
        self::createView_Navigation();
        self::createView_PageContent();
        self::createView_Permissions();
    }

    private static function createDatabase($db)
    {
        $installDB= "CREATE DATABASE IF NOT EXISTS " . $db['DB_DATABASE'];

        if(!self::$_connection->query($installDB))
            die("<h3 style=\"color: #E82418;\">Database could not be installed</h3>
                <p>This could be causes by multiple issues...</p>
                <ol>
                    <li>Your phpMyAdmin account may not have permission to create a new database</li>
                    <li>You already have a database created with the name \"$db[DB_DATABASE]\"</li>
                    <li>You have made inappropriate and unwarranted changes to the source code</li>
                </ol>
                <p>Please be sure to follow the guidelines when setting up your application</p>");
        else
            echo "<h3 style=\"color: #18E843\">Your database was successfully installed!</h3><br/>";
    }

    private static function createUsers($db)
    {
        // Create Table
        $QUser = "CREATE TABLE IF NOT EXISTS `Q_USERS` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `username` blob NOT NULL DEFAULT '',
            `email` blob NOT NULL DEFAULT '',
            `password` varchar(255) NOT NULL DEFAULT '',
            `token` varchar(255) NOT NULL DEFAULT '',
            `firstName` blob NOT NULL DEFAULT '',
            `lastName` blob NOT NULL DEFAULT '',
            `active` tinyint(255) NOT NULL DEFAULT '1',
            `permissionID` int(1) NOT NULL DEFAULT 1,
            `datecreated` datetime NOT NULL,
            `lastsignin` datetime NOT NULL,
            PRIMARY KEY (`ID`))";

        if(!$result = self::$_connection->query($QUser))
            die(self::$_connection->error);

        // Create First User
        $defaultUser = "INSERT INTO `Q_USERS` (`ID`, `username`, `email`, `password`, `token`,
            `firstName`, `lastName`, `active`, `permissionID`, `datecreated`)
            VALUES (1,AES_ENCRYPT('admin', '$db[APP_ENCRYPT_KEY]'),
                AES_ENCRYPT('admin@email.com', '$db[APP_ENCRYPT_KEY]'),
                '$2y$10$4QYVuQ8FEz3p4rQ5ykb6RO.FeSG4yvzP.8r00zX/3j63S5r5t5VUK',
                '',
                AES_ENCRYPT('admin', '$db[APP_ENCRYPT_KEY]'),
                AES_ENCRYPT('admin', '$db[APP_ENCRYPT_KEY]'),
                1,5, NOW())";

        if(!$result = self::$_connection->query($defaultUser))
            die(self::$_connection->error);
    }

    private static function createUserPermissions($db)
    {
        $QPerms = "CREATE TABLE IF NOT EXISTS `Q_USER_PERMISSION_LVLS` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(55) NOT NULL DEFAULT '',
            `description` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`ID`))";

        if(!$result = self::$_connection->query($QPerms))
            die(self::$_connection->error);

        $levels =
        [
            "User" => "Baseline priviledges to use the site.",
            "MVP" => "A user who has shown they are reliable and can be counted on for menial
                moderation tasks.",
            "Moderator" => "A graduated MVP or paid employee who moderates parts of the website.",
            "Administrator" => "A person with full control over the creation, deletion, and content
                of every webpage. They can also promote Users to MVP, and invite new people to the
                admin section.",
            "Master" => "The person with full control over the assignment of every previous role and
                the creation, deletion, content of every webpage. Only the Master can assign
                Administrators, and Moderators."
        ];
        foreach($levels as $key => $value)
        {
            $lvl = "INSERT INTO `Q_USER_PERMISSION_LVLS` (`title`, `description`)
                VALUES ('$key', '$value')";
            self::$_connection->query($lvl);
        }
    }

    private static function createPageContents($db)
    {
        // Page Content Text
        $QPages = "CREATE TABLE IF NOT EXISTS `Q_PAGES_CONTENT_TEXT` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `paragraph` text NOT NULL DEFAULT '',
            `pageID` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`ID`))";

        if(!$result = self::$_connection->query($QPages))
            die(self::$_connection->error);

        // Page Content Image
        $QPages = "CREATE TABLE IF NOT EXISTS `Q_PAGES_CONTENT_IMG` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `image` text NOT NULL DEFAULT '',
            `pageID` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`ID`))";

        if(!$result = self::$_connection->query($QPages))
            die(self::$_connection->error);
    }

    private static function createPages($db)
    {
        // Page
        $QPages = "CREATE TABLE IF NOT EXISTS `Q_PAGES` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`ID`))";

        if(!$result = self::$_connection->query($QPages))
            die(self::$_connection->error);

        // Default Page Creation
        $pages = ['Home', 'About', 'Contact'];
        foreach($pages as $key => $value)
        {
            $pg = "INSERT INTO `Q_PAGES` (`name`) VALUES ('$value')";
            if(!$result = self::$_connection->query($pg))
                die(self::$_connection->error);
        }
    }

    private static function createView_Navigation()
    {
        $view = "CREATE VIEW main_navigation AS SELECT `name` FROM `Q_PAGES`";
        self::$_connection->query($view);
    }

    private static function createView_PageContent()
    {
        $pages = ["Home", "About", "Contact"];
        foreach($pages as $key => $value)
        {
            $view = "CREATE VIEW ".$value."_content_txt AS SELECT `Q_PAGES`.`name`,
                `Q_PAGES_CONTENT_TEXT`.`paragraph`
                FROM `Q_PAGES`
                LEFT JOIN `Q_PAGES_CONTENT_TEXT` ON `Q_PAGES_CONTENT_TEXT`.`pageID` = `Q_PAGES`.`ID`
                WHERE `Q_PAGES`.`name` = '$value'";
            self::$_connection->query($view);

            $view = "CREATE VIEW ".$value."_content_img AS SELECT `Q_PAGES_CONTENT_IMG`.`image`
                FROM `Q_PAGES`
                LEFT JOIN `Q_PAGES_CONTENT_IMG` ON `Q_PAGES_CONTENT_IMG`.`pageID` = `Q_PAGES`.`ID`
                WHERE `Q_PAGES`.`name` = '$value'";
            self::$_connection->query($view);
        }
    }

    private static function createView_Permissions()
    {
        $view = "CREATE VIEW permissions AS SELECT `title`, `description` FROM `Q_USER_PERMISSION_LVLS`";
        self::$_connection->query($view);
    }

    private static function assembleQuery_SELECT_ENCRYPTED($data)
    {
        $env = Env::fetchEnv();
        $select = "";
        $i = 1;
        foreach($data['encrypted'] as $key => $value)
        {
            $select .= "AES_DECRYPT(`$value`, '$env[APP_ENCRYPT_KEY]') as `$value`";
            if($i != count($data['encrypted']))
                $select .= ", ";
            else
                if(isset($data['unencrypted']))
                    if(count($data['unencrypted']))
                        $select .= ", ";
                    else
                        $select .= " ";
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

    private static function assembleQuery_WHERE_ENCRYPTED($data)
    {
        $env = Env::fetchEnv();
        $i = 1;
        $where = "WHERE ";
        foreach($data['encrypted'] as $key => $value)
        {
            $where .= "AES_DECRYPT(`$key`, '$env[APP_ENCRYPT_KEY]') = '$value'";
            if($i != count($data['encrypted']))
                $where .= "AND ";
            $i++;
        }
        if(isset($data['unencrypted']))
            if(count($data['unencrypted']))
                $where .= "AND ";
            else
                $where .= " ";
        else
            $where .= " ";
        return $where;
    }

    private static function assembleQuery_WHERE_UNENCRYPTED($data)
    {
        $i = 1;
        $where = "WHERE ";
        foreach($data['unencrypted'] as $key => $value)
        {
            $where .= "`$key` = '$value'";
            if($i != count($data['unencrypted']))
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

