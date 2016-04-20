<?php
namespace Application\Env;

class Env
{
    private static $_env = [];

    public static function createEnviornment()
    {
        $file = fopen("../../.env", "r");
        $file = fread($file, filesize("../../.env"));
        $file = explode(",", $file);
        foreach($file as $key => $value)
        {
            $piece = explode("=>", $value);
            $_env[$piece[0]] = $piece[1];    
        }
        fclose($file);
    }

    public static function fetchEnv()
    {
        return $_env;
    }
}