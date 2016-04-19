<?php 
echo "<br/>AUTOLOAD<br/>";
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
spl_autoload_extensions('.php, .class.php');

/*** slice namespaces ***/
function rmNamespace($class)
{
    if(strpos($class, "\\") > 0) {
        $class = substr($class, strpos($class, "\\") + 1);
        return rmNamespace($class);
    }else{
        return $class;
    }
}

/*** class Loader ***/
function classLoader($class)
{
    $filename = strtolower(rmNamespace($class)) . '.class.php';
    $file = __DIR__ . "/classes/". $filename;
    if (!file_exists($file))
    {
        echo "$file Not Found!<br/>";
        return false;
    }
    include $file;
}

spl_autoload_register('classLoader');

