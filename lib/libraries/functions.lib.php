<?php
namespace Functions;

use Quasar\Kernel;

class Functions
{
    public static function helloWorld()
    {
        echo "Hello, World!<br/>";
    }

    /**
     * [genForm]
     *
     * Generates a form, the form can be blank upon load or it can prepopulated assuming that
     * the provided prepopulate array contains keys that match up with the keys in the $fields
     * argument.
     *
     * @param  [string]  $name          [name of the form]
     * @param  [string]  $method        [method, usually a POST]
     * @param  [string]  $action        [where to post to]
     * @param  [array]   $fields        [array of all inputs and their names]
     * @param  [mixed]   $prepopulate   [optional, will auto populate every input]
     * @return [string]                 [HTML form]
     */
    public static function genForm($name, $method, $action, $fields, $prepopulate = false)
    {
        $formStr = "<form name=\"$name\" method=\"$method\" action=\"$action\">";
        foreach($fields as $k => $v)
        {
            $default = "";
            if(isset($prepopulate[$k]))
                $default = $prepopulate[$k];
            $input = self::analyzeFormField($k, $v['type'], $default);
            $formStr .= "<label for=\"$k\">$v[label]</label>";
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

    private static function analyzeFormField($name, $type, $default)
    {
        $input = "";
        if($type == "text" || $type == "password" || $type == "email")
            $input = "<input type=\"$type\" name=\"$name\" value=\"$default\" required />";
        else if($type == "textarea")
            $input = "<textarea name=\"$name\" required></textarea>";
        else
            return $input;
        return $input;
    }
}