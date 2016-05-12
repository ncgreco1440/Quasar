<?php
require_once __DIR__."/vendor/autoload.php";

/*** nullify any existing autoloads ***/
spl_autoload_register(null, false);

/*** specify extensions that may be loaded ***/
spl_autoload_extensions('.php, .class.php, lib.php, contrl.php');

/*** slice namespaces ***/
function rmNamespace($name)
{
    if(strpos($name, "\\") > 0) {
        $name = substr($name, strpos($name, "\\") + 1);
        return rmNamespace($name);
    }else{
        return $name;
    }
}

/*** slice namespaces ***/
function rmNamespace_dir($name)
{
    if(substr_count($name, "\\") >= 2) {
        $name = substr($name, strpos($name, "\\") + 1);
        return rmNamespace_dir($name);
    }else{
        $result = explode("\\", $name);
        return ['dir' => $result[0], 'file' => strtolower($result[1])];
    }
}

/*** class Loader ***/
function classLoader($class)
{
    $filename = strtolower(rmNamespace($class)) . '.class.php';
    $file = __DIR__ . "/classes/". $filename;
    if (!file_exists($file))
        return false;
    include $file;
}

/*** library Loader ***/
function libLoader($library)
{
    $filename = strtolower(rmNamespace($library)) . '.lib.php';
    $file = __DIR__ . "/libraries/". $filename;
    if (!file_exists($file))
        return false;
    include $file;
}

/*** library Loader ***/
function contrlLoader($contrl)
{
    $filename = strtolower(rmNamespace($contrl)) . '.contrl.php';
    $file = __DIR__ . "/controllers/". $filename;
    if (!file_exists($file))
        return false;
    include $file;
}

/*** vendor loader ***/
function vendorLoader($vend)
{
    $filename = rmNamespace_dir($vend);
    $file = __DIR__ . "/vendors/". $filename['dir'] . "/" . $filename['file'] . ".class.php";
    if (!file_exists($file))
        return false;
    include $file;
}

spl_autoload_register('classLoader');
spl_autoload_register('libLoader');
spl_autoload_register('contrlLoader');
spl_autoload_register('vendorLoader');

/*** Immediately establish a connection to the database ***/
use Database;
Database\Connection::connect();
