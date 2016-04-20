 <?php
    // Just a single include statement!
    require "../lib/autoload.php";
    use Database;

    $benchmark = new Ubench;
    $benchmark->start();

    $web = new Website("Leverage");
    $webpage = new Webpage("A New Web Page");
    Database\Connection::connect();
    Database\Extra::invoked();
    $benchmark->end();
    echo "Time to load: " . $benchmark->getTime() . "<br/>"; // 156ms or 1.123s
    //echo $benchmark->getTime(true) . "<br/>"; // elapsed microtime in float
    //echo $benchmark->getTime(false, '%d%s') . "<br/>"; // 156ms or 1s

    echo "Memory Peak: " . $benchmark->getMemoryPeak() . "<br/>"; // 152B or 90.00Kb or 15.23Mb
    //echo "Memory Peak: " . $benchmark->getMemoryPeak(true) . "bytes<br/>"; // memory peak in bytes
    //echo $benchmark->getMemoryPeak(false, '%.3f%s') . "<br/>"; // 152B or 90.152Kb or 15.234Mb

    // Returns the memory usage at the end mark
    echo "Memory Usage: " . $benchmark->getMemoryUsage(); // 152B or 90.00Kb or 15.23Mb
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Tech - Start Up</title>
    </head>
    <body>
    </body>
</html>