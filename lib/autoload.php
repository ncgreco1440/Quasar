<?php
/** ====================================================================================

    1.

    The SPL __autoload() method is one of the Magic Methods supplied in PHP.
    The __autoload method is called whenever a class is instantiated and
    will load the classs the the first time it is called. No longer is
    include(), require(), include_once() or require_once() needed
    as the SPL autoload takes care of this interally.

====================================================================================*/



/** ====================================================================================

    2.

    In its simplest form, the SPL autoload class can find all class files
    in a directory, where the class the class names, matches the file
    name. This is great for maintaining a naming convention
    through-out projects.

====================================================================================*/

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
