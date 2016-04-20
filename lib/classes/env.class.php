<?php
namespace Application\Env;

class Env
{
    private static $_env = [];

    public static function createEnviornment()
    {
        self::setGlobalVars();
        self::setErrorReporting();
    }

    public static function fetchEnv()
    {
        return self::$_env;
    }

    /**
     * =============================================================================================
     * [setGlobalVars]
     *
     * Sets the global variables within the $_env array for this
     * particular application. This function internally reads
     * the .env file in the home directory. Any extra
     * global variables should be added to that
     * file and that file only.
     * =============================================================================================
     */
    private static function setGlobalVars()
    {
        // Set Global Environment Variables
        $file = fopen("../.env", "r");
        $file = fread($file, filesize("../.env"));
        $file = explode(",", $file);
        foreach($file as $key => $value)
        {
            $piece = explode("=>", $value);
            self::$_env[trim($piece[0])] = trim($piece[1]);
        }
        fclose($file);
    }

    private static function setErrorReporting()
    {
        // Error Reporting
        if(self::$_env['APP_ENV'] == 'local')
        {
            ini_set('display_errors', 1);
            ini_set('log_errors', 1);
            error_reporting(E_ALL);
        }
        else if(self::$_env['APP_ENV'] == 'production')
        {
            ini_set('display_errors', 0);
            ini_set('log_errors', 0);
            error_reporting(0);
        }
        else
            die("<strong>ERROR</strong> Incorrect Enviornment Value <strong>APP_ENV</strong> set in
                <strong>.env</strong> you may choose only between \"local\" and \"production\".");
    }
}