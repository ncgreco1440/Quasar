<?php
namespace Functions;

use Quasar\Kernel;

class Functions
{
    public static function helloWorld()
    {
        echo "Hello, World!<br/>";
    }

    public static function genForm($name, $method, $action, $fields)
    {
        $formStr = "<form name=\"$name\" method=\"$method\" action=\"$action\">";
        foreach($fields as $k => $v)
        {
            $input = self::analyzeFormField($k, $v);
            $formStr .= "<label for=\"$k\">$k</label>";
            $formStr .= $input;
        }
        $formStr .= "<input class=\"button\" name=\"$name\""."Submit"." type=\"submit\"
            value=\"submit\" />";
        $formStr .= "</form>";
        return $formStr;
    }

    public static function yieldCopyright()
    {
        $str = "&copy; Copyright 2016";
        $str .= date('Y') > 2016 ? " - " .date('Y') : "";
        return $str;
    }

    public static function displayFormMsg($message)
    {
        $status;
        $message['success'] == true ? $status = "success" : $status = "error";
        echo "<div class=\"form-row\">
            <div class=\"center-inline-items\">
            <span class=\"message $status\">$message[message]</span>
            </div>
            </div>";
    }

    private static function analyzeFormField($name, $type)
    {
        $input = "";
        if($type == "text" || $type == "password" || $type == "email")
            $input = "<input type=\"$type\" name=\"$name\" value=\"\" required />";
        else if($type == "textarea")
            $input = "<textarea name=\"$name\" required></textarea>";
        else
            return $input;
        return $input;
    }
}